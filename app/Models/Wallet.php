<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'account_number',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
