<?php

namespace App\Repositories;

use App\Models\MerchantWallet;
use App\Models\SellerTransaction;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\DB;

class SellerWalletRepository
{
    public function findWalletByUserId(int $userId): ?MerchantWallet
    {
        return MerchantWallet::where('user_id', $userId)->first();
    }

    public function createWalletForUser(int $userId): MerchantWallet
    {
        return MerchantWallet::create(['user_id' => $userId, 'balance' => 0, 'status' => 'active']);
    }

    public function incrementBalance(MerchantWallet $wallet, float $amount): MerchantWallet
    {
        $wallet->balance = $wallet->balance + $amount;
        $wallet->save();
        return $wallet->refresh();
    }

    public function decrementBalance(MerchantWallet $wallet, float $amount): MerchantWallet
    {
        if ($wallet->balance < $amount) {
            throw new \Exception('Insufficient merchant wallet balance');
        }
        $wallet->balance = $wallet->balance - $amount;
        $wallet->save();
        return $wallet->refresh();
    }

    public function recordTransaction(array $data): SellerTransaction
    {
        return SellerTransaction::create($data);
    }

    public function existsTransactionByReference(string $reference): bool
    {
        return SellerTransaction::where('reference', $reference)->exists();
    }

    public function createWithdrawRequest(array $data): WithdrawRequest
    {
        return WithdrawRequest::create($data);
    }

    public function getTransactionsByUser(int $userId, int $perPage = 15)
    {
        return SellerTransaction::where('user_id', $userId)->latest()->paginate($perPage);
    }

    public function getSummary(int $userId): array
    {
        $totalIncome = SellerTransaction::where('user_id', $userId)->where('type', 'credit')->sum('amount');
        $totalDebit = SellerTransaction::where('user_id', $userId)->where('type', 'debit')->sum('amount');
        $pendingWithdraw = WithdrawRequest::where('user_id', $userId)->where('status', 'pending')->sum('amount');

        return [
            'total_income' => (float) $totalIncome,
            'total_debit' => (float) $totalDebit,
            'pending_withdraw' => (float) $pendingWithdraw,
            'net' => (float) ($totalIncome - $totalDebit),
        ];
    }

    public function findWithdrawById(int $id)
    {
        return WithdrawRequest::find($id);
    }

    public function findWithdrawByIdForUpdate(int $id)
    {
        return WithdrawRequest::where('id', $id)->lockForUpdate()->first();
    }

    public function updateWithdrawStatus($withdraw, array $data)
    {
        $withdraw->fill($data);
        $withdraw->save();
        return $withdraw->refresh();
    }

    public function findWalletByUserIdForUpdate(int $userId)
    {
        return MerchantWallet::where('user_id', $userId)->lockForUpdate()->first();
    }
}
