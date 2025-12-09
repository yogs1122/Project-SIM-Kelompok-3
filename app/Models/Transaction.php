<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'status',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
