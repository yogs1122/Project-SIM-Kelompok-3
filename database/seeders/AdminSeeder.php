<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat admin account saja
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'yogs1122@gmail.com',
            'phone' => '+628000000001',
            'password' => Hash::make('112233'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
    }
}