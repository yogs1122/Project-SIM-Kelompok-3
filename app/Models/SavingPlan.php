<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SavingPlan extends Model
{
    use HasFactory;

    // ========== CONFIG ==========
    protected $fillable = [
        'user_id', 'title', 'description', 'target_amount', 'current_amount',
        'start_date', 'end_date', 'category', 'color_code', 'icon',
        'auto_save', 'auto_save_amount', 'auto_save_frequency', 'status', 'is_public'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'auto_save_amount' => 'decimal:2',
        'auto_save' => 'boolean',
        'is_public' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $appends = [
        'progress_percentage', 'days_left', 'required_monthly',
        'formatted_target', 'formatted_current', 'is_due_soon', 'is_on_track'
    ];

    // ========== RELATIONSHIPS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(SavingTransaction::class, 'plan_id');
    }

    // ========== ACCESSORS ==========
    public function getProgressPercentageAttribute()
    {
        return $this->target_amount > 0 
            ? round(min(100, ($this->current_amount / $this->target_amount) * 100), 2)
            : 0;
    }

    public function getDaysLeftAttribute()
    {
        return max(0, Carbon::parse($this->end_date)->diffInDays(now(), false));
    }

    public function getRequiredMonthlyAttribute()
    {
        $monthsLeft = max(1, Carbon::parse($this->end_date)->diffInMonths(now(), false));
        $remaining = max(0, $this->target_amount - $this->current_amount);
        return ceil($remaining / $monthsLeft);
    }

    public function getFormattedTargetAttribute()
    {
        return 'Rp ' . number_format($this->target_amount, 0, ',', '.');
    }

    public function getFormattedCurrentAttribute()
    {
        return 'Rp ' . number_format($this->current_amount, 0, ',', '.');
    }

    public function getIsDueSoonAttribute()
    {
        return $this->status === 'active' && $this->days_left <= 7;
    }

    public function getIsOnTrackAttribute()
    {
        if ($this->days_left <= 0) return true;
        
        $totalDays = Carbon::parse($this->start_date)->diffInDays($this->end_date);
        $daysPassed = max(0, Carbon::parse($this->start_date)->diffInDays(now(), false));
        
        $expectedProgress = $totalDays > 0 ? ($daysPassed / $totalDays) * 100 : 0;
        return $this->progress_percentage >= $expectedProgress;
    }

    // ========== SCOPES ==========
    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }
    public function scopeByUser($query, $userId) { return $query->where('user_id', $userId); }
    public function scopeByCategory($query, $category) { return $query->where('category', $category); }

    // ========== BUSINESS LOGIC ==========
    public function addFunds($amount, $paymentMethod = 'wallet', $notes = '')
    {
        \DB::beginTransaction();
        try {
            // Update plan
            $this->current_amount += $amount;
            if ($this->current_amount >= $this->target_amount) {
                $this->status = 'completed';
                $this->current_amount = $this->target_amount;
            }
            $this->save();

            // Create transaction
            SavingTransaction::create([
                'plan_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'transaction_type' => 'deposit',
                'payment_method' => $paymentMethod,
                'notes' => $notes,
                'status' => 'completed'
            ]);

            \DB::commit();
            return ['success' => true, 'plan' => $this];

        } catch (\Exception $e) {
            \DB::rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function cancelPlan()
    {
        $this->status = 'cancelled';
        $this->save();
        
        // Optional: Log cancellation transaction
        if ($this->current_amount > 0) {
            SavingTransaction::create([
                'plan_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $this->current_amount,
                'transaction_type' => 'withdrawal',
                'payment_method' => 'refund',
                'notes' => 'Plan cancellation',
                'status' => 'completed'
            ]);
        }
        
        return true;
    }

    public function getStats()
    {
        return [
            'total_deposits' => $this->transactions()->deposits()->sum('amount'),
            'total_withdrawals' => $this->transactions()->withdrawals()->sum('amount'),
            'transaction_count' => $this->transactions()->count(),
            'last_transaction' => $this->transactions()->latest()->first(),
            'completion_rate' => $this->progress_percentage
        ];
    }

    // ========== STATIC METHODS ==========
    public static function getUserStats($userId)
    {
        $plans = self::where('user_id', $userId)->active()->get();
        
        return [
            'total_savings' => $plans->sum('current_amount'),
            'total_targets' => $plans->sum('target_amount'),
            'overall_progress' => $plans->sum('target_amount') > 0 
                ? round(($plans->sum('current_amount') / $plans->sum('target_amount')) * 100, 2)
                : 0,
            'active_plans' => $plans->count(),
            'total_completed' => self::where('user_id', $userId)->completed()->count()
        ];
    }
}