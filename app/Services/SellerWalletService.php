<?php

namespace App\Services;

use App\Repositories\SellerWalletRepository;
use Illuminate\Support\Facades\DB;

class SellerWalletService
{
    protected SellerWalletRepository $repo;

    public function __construct(SellerWalletRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Credit merchant wallet from a buyer payment (atomic + idempotent by reference)
     *
     * @param int $sellerUserId
     * @param float $amount
     * @param int|null $sourceUserId
     * @param string|null $reference  Idempotency key / order id
     * @param array $meta
     * @return array
     */
    public function creditFromBuyerPayment(int $sellerUserId, float $amount, ?int $sourceUserId = null, ?string $reference = null, array $meta = []): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        if ($reference && $this->repo->existsTransactionByReference($reference)) {
            return ['success' => true, 'message' => 'Already processed', 'reference' => $reference];
        }

        return DB::transaction(function () use ($sellerUserId, $amount, $sourceUserId, $reference, $meta) {
            $wallet = $this->repo->findWalletByUserId($sellerUserId) ?? $this->repo->createWalletForUser($sellerUserId);

            // Increment balance
            $this->repo->incrementBalance($wallet, $amount);

            // Record transaction
            $tx = $this->repo->recordTransaction([
                'user_id' => $sellerUserId,
                'type' => 'credit',
                'subtype' => 'sale',
                'amount' => $amount,
                'fee' => $meta['fee'] ?? 0,
                'source_user_id' => $sourceUserId,
                'target_user_id' => $sellerUserId,
                'reference' => $reference,
                'meta' => $meta,
            ]);

            return ['success' => true, 'transaction' => $tx];
        });
    }

    /**
     * Debit merchant wallet for withdraw: create withdraw request and reserve/debit funds atomically
     *
     * @param int $sellerUserId
     * @param float $amount
     * @param string $bankAccount
     * @param string|null $bankName
     * @param int|null $requestedBy
     * @return array
     */
    public function debitForWithdraw(int $sellerUserId, float $amount, string $bankAccount, ?string $bankName = null, ?int $requestedBy = null): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        return DB::transaction(function () use ($sellerUserId, $amount, $bankAccount, $bankName, $requestedBy) {
            $wallet = $this->repo->findWalletByUserId($sellerUserId);
            if (!$wallet) {
                throw new \Exception('Merchant wallet not found');
            }

            // ensure sufficient funds
            $this->repo->decrementBalance($wallet, $amount);

            // create withdraw request (status pending)
            $withdraw = $this->repo->createWithdrawRequest([
                'user_id' => $sellerUserId,
                'amount' => $amount,
                'bank_account' => $bankAccount,
                'bank_name' => $bankName,
                'status' => 'pending',
                'meta' => ['requested_by' => $requestedBy],
            ]);

            // record transaction as debit (withdraw request)
            $tx = $this->repo->recordTransaction([
                'user_id' => $sellerUserId,
                'type' => 'debit',
                'subtype' => 'withdraw',
                'amount' => $amount,
                'fee' => 0,
                'source_user_id' => $sellerUserId,
                'target_user_id' => null,
                'reference' => 'withdraw:' . $withdraw->id,
                'meta' => ['withdraw_id' => $withdraw->id],
            ]);

            return ['success' => true, 'withdraw' => $withdraw, 'transaction' => $tx];
        });
    }

    /**
     * Optional: transfer from seller merchant wallet to a user (external transfer)
     * This operation is allowed only when business rules permit.
     */
    public function transferToUser(int $sellerUserId, int $targetUserId, float $amount, ?string $reference = null, array $meta = []): array
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }

        return DB::transaction(function () use ($sellerUserId, $targetUserId, $amount, $reference, $meta) {
            $wallet = $this->repo->findWalletByUserId($sellerUserId);
            if (!$wallet) {
                throw new \Exception('Merchant wallet not found');
            }

            // debit seller wallet
            $this->repo->decrementBalance($wallet, $amount);

            // record debit transaction
            $debit = $this->repo->recordTransaction([
                'user_id' => $sellerUserId,
                'type' => 'debit',
                'subtype' => 'transfer_out',
                'amount' => $amount,
                'fee' => $meta['fee'] ?? 0,
                'source_user_id' => $sellerUserId,
                'target_user_id' => $targetUserId,
                'reference' => $reference,
                'meta' => $meta,
            ]);

            // credit target user's personal wallet (if exists) - simple approach: use user->wallet relation
            $target = \App\Models\User::find($targetUserId);
            if ($target && $target->wallet) {
                $target->wallet->balance = $target->wallet->balance + $amount;
                $target->wallet->save();

                $credit = $this->repo->recordTransaction([
                    'user_id' => $sellerUserId,
                    'type' => 'credit',
                    'subtype' => 'transfer_in',
                    'amount' => $amount,
                    'fee' => 0,
                    'source_user_id' => $sellerUserId,
                    'target_user_id' => $targetUserId,
                    'reference' => $reference,
                    'meta' => array_merge($meta, ['to_wallet' => 'user_wallet']),
                ]);
            }

            return ['success' => true, 'debit' => $debit];
        });
    }

    // Wrapper: get paginated transactions for seller
    public function getTransactions(int $sellerUserId, int $perPage = 15)
    {
        return $this->repo->getTransactionsByUser($sellerUserId, $perPage);
    }

    // Wrapper: get summary (total income, total debit, pending withdraw)
    public function getSummary(int $sellerUserId): array
    {
        return $this->repo->getSummary($sellerUserId);
    }

    /**
     * Admin: approve withdraw request. Will debit merchant wallet if not already debited.
     */
    public function adminApproveWithdraw(int $withdrawId, int $processorId): array
    {
        return DB::transaction(function () use ($withdrawId, $processorId) {
            $withdraw = $this->repo->findWithdrawByIdForUpdate($withdrawId);
            if (!$withdraw) {
                throw new \Exception('Withdraw request not found');
            }

            if ($withdraw->status !== 'pending') {
                return ['success' => false, 'message' => 'Withdraw not pending', 'status' => $withdraw->status];
            }

            // If a transaction with this reference exists, assume already debited
            $reference = 'withdraw:' . $withdraw->id;
            if (!$this->repo->existsTransactionByReference($reference)) {
                // lock merchant wallet
                $wallet = $this->repo->findWalletByUserIdForUpdate($withdraw->user_id);
                if (!$wallet) {
                    throw new \Exception('Merchant wallet not found');
                }

                if ($wallet->balance < $withdraw->amount) {
                    throw new \Exception('Insufficient merchant balance to approve withdraw');
                }

                // debit wallet
                $this->repo->decrementBalance($wallet, $withdraw->amount);

                // record transaction
                $this->repo->recordTransaction([
                    'user_id' => $withdraw->user_id,
                    'type' => 'debit',
                    'subtype' => 'withdraw',
                    'amount' => $withdraw->amount,
                    'fee' => 0,
                    'source_user_id' => $withdraw->user_id,
                    'target_user_id' => null,
                    'reference' => $reference,
                    'meta' => ['withdraw_id' => $withdraw->id, 'processed_by' => $processorId],
                ]);
            }

            $updated = $this->repo->updateWithdrawStatus($withdraw, [
                'status' => 'approved',
                'processed_by' => $processorId,
                'processed_at' => now(),
            ]);

            return ['success' => true, 'withdraw' => $updated];
        });
    }

    public function adminRejectWithdraw(int $withdrawId, int $processorId, ?string $reason = null): array
    {
        return DB::transaction(function () use ($withdrawId, $processorId, $reason) {
            $withdraw = $this->repo->findWithdrawByIdForUpdate($withdrawId);
            if (!$withdraw) {
                throw new \Exception('Withdraw request not found');
            }

            if ($withdraw->status !== 'pending') {
                return ['success' => false, 'message' => 'Withdraw not pending', 'status' => $withdraw->status];
            }

            // If the request was already debited earlier (rare), we should refund
            $reference = 'withdraw:' . $withdraw->id;
            if ($this->repo->existsTransactionByReference($reference)) {
                // refund: credit back
                $wallet = $this->repo->findWalletByUserIdForUpdate($withdraw->user_id);
                if (!$wallet) {
                    // create wallet and credit
                    $wallet = $this->repo->createWalletForUser($withdraw->user_id);
                }
                $this->repo->incrementBalance($wallet, $withdraw->amount);

                $this->repo->recordTransaction([
                    'user_id' => $withdraw->user_id,
                    'type' => 'credit',
                    'subtype' => 'withdraw_refund',
                    'amount' => $withdraw->amount,
                    'fee' => 0,
                    'source_user_id' => null,
                    'target_user_id' => $withdraw->user_id,
                    'reference' => $reference . ':refund',
                    'meta' => ['withdraw_id' => $withdraw->id, 'processed_by' => $processorId],
                ]);
            }

            $updated = $this->repo->updateWithdrawStatus($withdraw, [
                'status' => 'rejected',
                'processed_by' => $processorId,
                'processed_at' => now(),
                'meta' => array_merge($withdraw->meta ?? [], ['rejected_reason' => $reason])
            ]);

            return ['success' => true, 'withdraw' => $updated];
        });
    }

    public function adminCompleteWithdraw(int $withdrawId, int $processorId): array
    {
        return DB::transaction(function () use ($withdrawId, $processorId) {
            $withdraw = $this->repo->findWithdrawByIdForUpdate($withdrawId);
            if (!$withdraw) {
                throw new \Exception('Withdraw request not found');
            }

            if ($withdraw->status !== 'approved') {
                return ['success' => false, 'message' => 'Withdraw not approved', 'status' => $withdraw->status];
            }

            $updated = $this->repo->updateWithdrawStatus($withdraw, [
                'status' => 'completed',
                'processed_by' => $processorId,
                'processed_at' => now(),
            ]);

            return ['success' => true, 'withdraw' => $updated];
        });
    }
}
