<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SmartFinanceRecommendation extends Model
{
    use HasFactory;

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected $fillable = ['user_id', 'admin_id', 'message', 'rendered_message', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
