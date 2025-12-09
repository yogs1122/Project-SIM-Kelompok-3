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
        // 1. Buat admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@ewallet.com',
            'phone' => '+628000000001',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // 2. Buat user biasa
        $user = User::create([
            'name' => 'User Test',
            'email' => 'user@ewallet.com',
            'phone' => '+628000000002',
            'password' => Hash::make('user123'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
        
        // Buat wallet untuk user
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 500000,
            'account_number' => preg_replace('/\D+/', '', $user->phone),
        ]);
        
        // Buat sample transactions untuk user
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => 500000,
            'description' => 'Top up wallet',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(2),
        ]);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'transfer',
            'amount' => 100000,
            'description' => 'Transfer ke teman',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(1),
        ]);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => 50000,
            'description' => 'Pembayaran tagihan listrik',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
        ]);

        // 3. Buat UMKM
        $umkm = User::create([
            'name' => 'Toko UMKM',
            'email' => 'umkm@ewallet.com',
            'phone' => '+628000000003',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $umkm->assignRole('umkm');
        
        // Buat wallet untuk UMKM
        Wallet::create([
            'user_id' => $umkm->id,
            'balance' => 2000000,
            'account_number' => preg_replace('/\D+/', '', $umkm->phone),
        ]);
        
        // Buat sample transactions untuk UMKM
        Transaction::create([
            'user_id' => $umkm->id,
            'type' => 'topup',
            'amount' => 2000000,
            'description' => 'Top up wallet untuk toko',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(3),
        ]);
        
        Transaction::create([
            'user_id' => $umkm->id,
            'type' => 'payment',
            'amount' => 250000,
            'description' => 'Penjualan produk',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(1),
        ]);
    }
}