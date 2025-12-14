<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'reference', 'buyer_id', 'merchant_id', 'amount', 'payment_method', 'status', 'proof_path', 'expires_at', 'meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
        'expires_at' => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }
}
