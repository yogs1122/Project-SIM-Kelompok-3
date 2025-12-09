<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+628100000000',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
        
        // Buat wallet untuk test user
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 1000000,
            'account_number' => preg_replace('/\D+/', '', $user->phone),
        ]);
        
        // Buat sample transactions
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => 1000000,
            'description' => 'Top up wallet',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(3),
        ]);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'transfer',
            'amount' => 150000,
            'description' => 'Transfer ke teman',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(2),
        ]);
        
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => 75000,
            'description' => 'Pembayaran online',
            'status' => 'completed',
            'reference_number' => 'REF' . Str::random(12),
            'created_at' => now()->subDays(1),
        ]);
    }
}
