<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmTransaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'source', 'target', 'amount', 'fee', 'meta'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
