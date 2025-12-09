<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'price', 'stock',
        'category', 'images', 'sold_count', 'rating',
        'review_count', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}