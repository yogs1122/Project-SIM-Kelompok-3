<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id', 'user_id', 'amount', 'transaction_type',
        'payment_method', 'notes', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    protected $appends = ['formatted_amount', 'formatted_date'];

    // ========== RELATIONSHIPS ==========
    public function plan()
    {
        return $this->belongsTo(SavingPlan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== ACCESSORS ==========
    public function getFormattedAmountAttribute()
    {
        $prefix = $this->transaction_type === 'withdrawal' ? '-' : '+';
        return $prefix . ' Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->translatedFormat('d F Y H:i');
    }

    // ========== SCOPES ==========
    public function scopeDeposits($query) { return $query->where('transaction_type', 'deposit'); }
    public function scopeWithdrawals($query) { return $query->where('transaction_type', 'withdrawal'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }
    public function scopeByPlan($query, $planId) { return $query->where('plan_id', $planId); }
}