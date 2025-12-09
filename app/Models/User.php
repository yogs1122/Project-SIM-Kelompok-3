<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail

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
    ];

    // RELASI KE ROLE
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

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

    // TAMBAHKAN ROLE KE USER
    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->attach($role->id);
        }
    }

    // RELASI KE WALLET
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
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
}