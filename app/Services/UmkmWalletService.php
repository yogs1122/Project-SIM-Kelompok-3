<?php

namespace App\Services;

use App\Models\User;
use App\Models\UmkmTransaction;
use Illuminate\Support\Facades\DB;

class UmkmWalletService
{
    /**
     * Transfer from saldo_pribadi to another user (external transfer)
     */
    public function transferToUser(User $from, User $to, float $amount, float $fee = 0.0)
    {
        if ($from->id === $to->id) {
            throw new \InvalidArgumentException('Tidak bisa transfer ke diri sendiri');
        }

        return DB::transaction(function () use ($from, $to, $amount, $fee) {
            // Only allow from saldo_pribadi
            if ($from->saldo_pribadi < ($amount + $fee)) {
                throw new \Exception('Saldo pribadi tidak mencukupi');
            }

            $from->debitSaldoPribadi($amount + $fee);
            $to->creditSaldoPribadi($amount);

            $tx = UmkmTransaction::create([
                'user_id' => $from->id,
                'type' => 'transfer_out',
                'source' => 'saldo_pribadi',
                'target' => 'user:'.$to->id,
                'amount' => $amount,
                'fee' => $fee,
                'meta' => ['to' => $to->id]
            ]);

            // Record incoming tx for recipient
            UmkmTransaction::create([
                'user_id' => $to->id,
                'type' => 'transfer_in',
                'source' => 'user:'.$from->id,
                'target' => 'saldo_pribadi',
                'amount' => $amount,
                'fee' => 0,
                'meta' => ['from' => $from->id]
            ]);

            return $tx;
        });
    }

    /**
     * Internal transfer: saldo_toko -> saldo_pribadi
     */
    public function transferStoreToPersonal(User $user, float $amount)
    {
        return DB::transaction(function () use ($user, $amount) {
            if ($user->saldo_toko < $amount) {
                throw new \Exception('Saldo toko tidak mencukupi');
            }

            $user->debitSaldoToko($amount);
            $user->creditSaldoPribadi($amount);

            return UmkmTransaction::create([
                'user_id' => $user->id,
                'type' => 'internal_transfer',
                'source' => 'saldo_toko',
                'target' => 'saldo_pribadi',
                'amount' => $amount,
                'fee' => 0,
            ]);
        });
    }

    /**
     * Withdraw from saldo_toko to bank (creates a withdraw transaction record)
     */
    public function withdrawFromStore(User $user, float $amount, array $bankMeta = [])
    {
        return DB::transaction(function () use ($user, $amount, $bankMeta) {
            if ($user->saldo_toko < $amount) {
                throw new \Exception('Saldo toko tidak mencukupi');
            }

            // For now mark as debited immediately; in real flow, create pending withdraw
            $user->debitSaldoToko($amount);

            return UmkmTransaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'source' => 'saldo_toko',
                'target' => 'bank',
                'amount' => $amount,
                'fee' => 0,
                'meta' => $bankMeta,
            ]);
        });
    }

    /**
     * Credit store balance from a sale
     */
    public function creditStoreFromSale(User $user, float $amount, array $meta = [])
    {
        return DB::transaction(function () use ($user, $amount, $meta) {
            $user->creditSaldoToko($amount);
            return UmkmTransaction::create([
                'user_id' => $user->id,
                'type' => 'sale',
                'source' => 'sale',
                'target' => 'saldo_toko',
                'amount' => $amount,
                'fee' => 0,
                'meta' => $meta,
            ]);
        });
    }
}
