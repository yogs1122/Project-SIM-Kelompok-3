<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'business_name', 'owner_name', 'phone', 'address', 'document_path', 'status', 'admin_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
