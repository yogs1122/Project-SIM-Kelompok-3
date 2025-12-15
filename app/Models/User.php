<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\MerchantWallet;
// HAPUS BARIS INI: use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable // HAPUS: implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'saldo_pribadi' => 'decimal:2',
        'saldo_toko' => 'decimal:2',
    ];

    // Tambahkan ini jika ingin attributes otomatis tersedia
    protected $appends = [
        'total_savings',
        'total_savings_target',
        'savings_progress',
        'total_balance',
        'available_balance',
        'locked_balance'
    ];

    // ==================== EXISTING RELATIONSHIPS ====================
    
    // RELASI KE ROLE
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // RELASI KE WALLET
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // RELASI KE MERCHANT WALLET
    public function merchantWallet()
    {
        return $this->hasOne(MerchantWallet::class);
    }

    // RELASI KE TRANSACTIONS
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // RELASI KE SALES FORUM POSTS
    public function salesForumPosts()
    {
        return $this->hasMany(SalesForumPost::class);
    }

    // ==================== NEW: SAVINGS RELATIONSHIPS ====================
    
    /**
     * RELASI KE SAVING PLANS (BARU)
     */
    public function savingPlans()
    {
        return $this->hasMany(SavingPlan::class);
    }

    /**
     * RELASI KE SAVING TRANSACTIONS (BARU)
     */
    public function savingTransactions()
    {
        return $this->hasMany(SavingTransaction::class);
    }

    // ==================== EXISTING METHODS ====================
    
    // CEK JIKA USER ADMIN
    public function isAdmin()
    {
        return $this->roles()->where('name', 'admin')->exists();
    }

    // CEK JIKA USER UMKM
    public function isUMKM()
    {
        return $this->roles()->where('name', 'umkm')->exists();
    }

    // CEK JIKA USER SELLER/MERCHANT
    public function isSeller()
    {
        return $this->roles()->whereIn('name', ['seller', 'merchant', 'umkm'])->exists();
    }

    // TAMBAHKAN ROLE KE USER
    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->attach($role->id);
        }
    }

    // ==================== NEW: SAVINGS METHODS ====================
    
    /**
     * Get active saving plans
     */
    public function activeSavingPlans()
    {
        return $this->savingPlans()->where('status', 'active');
    }

    /**
     * Get completed saving plans
     */
    public function completedSavingPlans()
    {
        return $this->savingPlans()->where('status', 'completed');
    }

    /**
     * Get total savings amount from all active plans
     */
    public function getTotalSavingsAttribute()
    {
        return $this->savingPlans()->where('status', 'active')->sum('current_amount');
    }

    /**
     * Get total savings target from all active plans
     */
    public function getTotalSavingsTargetAttribute()
    {
        return $this->savingPlans()->where('status', 'active')->sum('target_amount');
    }

    /**
     * Get overall savings progress percentage
     */
    public function getSavingsProgressAttribute()
    {
        $totalTarget = $this->total_savings_target;
        return $totalTarget > 0 
            ? round(($this->total_savings / $totalTarget) * 100, 2)
            : 0;
    }

    /**
     * Check if user has any saving plans
     */
    public function hasSavingPlans()
    {
        return $this->savingPlans()->count() > 0;
    }

    /**
     * Get recent saving transactions
     */
    public function recentSavingTransactions($limit = 10)
    {
        return $this->savingTransactions()
            ->with('plan')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Create a new saving plan
     */
    public function createSavingPlan($data)
    {
        return $this->savingPlans()->create(array_merge($data, [
            'user_id' => $this->id,
            'status' => 'active'
        ]));
    }

    // ==================== NEW: BALANCE METHODS ====================
    
    /**
     * Get balance from wallet (jika ada) + savings
     */
    public function getTotalBalanceAttribute()
    {
        $walletBalance = $this->wallet ? $this->wallet->balance : 0;
        return $walletBalance + $this->total_savings;
    }

    /**
     * Get available balance (wallet saja, tidak termasuk savings)
     */
    public function getAvailableBalanceAttribute()
    {
        return $this->wallet ? $this->wallet->balance : 0;
    }

    /**
     * Get locked balance (savings yang tidak bisa ditarik langsung)
     */
    public function getLockedBalanceAttribute()
    {
        return $this->total_savings;
    }

    /**
     * Check if user has enough available balance
     */
    public function hasSufficientBalance($amount)
    {
        return $this->available_balance >= $amount;
    }

    // UMKM specific balances
    public function getSaldoPribadiAttribute($value)
    {
        return $value === null ? 0.00 : (float) $value;
    }

    public function getSaldoTokoAttribute($value)
    {
        return $value === null ? 0.00 : (float) $value;
    }

    public function creditSaldoPribadi($amount)
    {
        $this->increment('saldo_pribadi', $amount);
        $this->refresh();
    }

    public function debitSaldoPribadi($amount)
    {
        if ($this->saldo_pribadi < $amount) {
            throw new \Exception('Saldo pribadi tidak mencukupi');
        }
        $this->decrement('saldo_pribadi', $amount);
        $this->refresh();
    }

    public function creditSaldoToko($amount)
    {
        $this->increment('saldo_toko', $amount);
        $this->refresh();
    }

    public function debitSaldoToko($amount)
    {
        if ($this->saldo_toko < $amount) {
            throw new \Exception('Saldo toko tidak mencukupi');
        }
        $this->decrement('saldo_toko', $amount);
        $this->refresh();
    }

    /**
     * Transfer from wallet to savings
     */
    public function transferToSavings($planId, $amount)
    {
        if (!$this->hasSufficientBalance($amount)) {
            return ['success' => false, 'message' => 'Saldo tidak mencukupi'];
        }

        $plan = $this->savingPlans()->where('id', $planId)->first();
        if (!$plan) {
            return ['success' => false, 'message' => 'Rencana tabungan tidak ditemukan'];
        }

        // Kurangi wallet balance
        if ($this->wallet) {
            $this->wallet->balance -= $amount;
            $this->wallet->save();
        }

        // Tambah ke saving plan
        $result = $plan->addFunds($amount, 'wallet_transfer', 'Transfer dari wallet');

        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Transfer berhasil',
                'new_wallet_balance' => $this->wallet ? $this->wallet->balance : 0,
                'plan' => $plan
            ];
        }

        return ['success' => false, 'message' => 'Transfer gagal'];
    }
}