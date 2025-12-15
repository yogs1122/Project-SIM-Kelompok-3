<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    // Top Up
    public function topupIndex()
    {
        // Prevent admin users from accessing top-up UI
        if (Auth::user() && Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Admin tidak diperbolehkan melakukan top up.');
        }

        $wallet = Auth::user()->wallet;
        return view('transactions.topup', compact('wallet'));
    }

    public function topupStore(Request $request)
    {
        // Prevent admin from performing top-up actions
        if (Auth::user() && Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Admin tidak diperbolehkan melakukan top up.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return back()->with('error', 'Wallet tidak ditemukan');
        }

        // Update wallet balance
        $wallet->update([
            'balance' => $wallet->balance + $validated['amount']
        ]);

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => $validated['amount'],
            'description' => 'Top up wallet',
            'status' => 'completed',
            'reference_number' => 'TUP' . Str::random(12),
        ]);

        return back()->with('success', 'Top up berhasil! Saldo Anda: Rp ' . number_format($wallet->balance + $validated['amount'], 0, ',', '.'));
    }

    // Transfer
    public function transferIndex()
    {
        $wallet = Auth::user()->wallet;
        $users = User::where('id', '!=', Auth::id())
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['user', 'umkm']);
            })
            ->get();
        return view('transactions.transfer', compact('wallet', 'users'));
    }

    public function transferStore(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id|not_in:' . Auth::id(),
            'amount' => 'required|numeric|min:1000|max:10000000',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;
        $recipient = User::findOrFail($validated['recipient_id']);
        $recipientWallet = $recipient->wallet;

        if (!$wallet) {
            return back()->with('error', 'Wallet Anda tidak ditemukan');
        }

        if (!$recipientWallet) {
            return back()->with('error', 'Wallet penerima tidak ditemukan');
        }

        if ($wallet->balance < $validated['amount']) {
            return back()->with('error', 'Saldo tidak cukup!');
        }

        // Deduct from sender
        $wallet->update([
            'balance' => $wallet->balance - $validated['amount']
        ]);

        // Add to recipient
        $recipientWallet->update([
            'balance' => $recipientWallet->balance + $validated['amount']
        ]);

        $refNumber = 'TRF' . Str::random(12);

        // Create transaction for sender
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'transfer',
            'amount' => $validated['amount'],
            'description' => 'Transfer ke ' . $recipient->name . ' (' . ($validated['description'] ?? '') . ')',
            'status' => 'completed',
            'reference_number' => $refNumber,
        ]);

        // Create transaction for recipient
        Transaction::create([
            'user_id' => $recipient->id,
            'type' => 'transfer',
            'amount' => $validated['amount'],
            'description' => 'Transfer dari ' . $user->name,
            'status' => 'completed',
            'reference_number' => $refNumber,
        ]);

        return back()->with('success', 'Transfer berhasil ke ' . $recipient->name . '! Saldo Anda: Rp ' . number_format($wallet->balance - $validated['amount'], 0, ',', '.'));
    }

    // Withdraw
    public function withdrawIndex()
    {
        $wallet = Auth::user()->wallet;
        return view('transactions.withdraw', compact('wallet'));
    }

    public function withdrawStore(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:50000|max:10000000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return back()->with('error', 'Wallet tidak ditemukan');
        }

        if ($wallet->balance < $validated['amount']) {
            return back()->with('error', 'Saldo tidak cukup!');
        }

        // Deduct from wallet
        $wallet->update([
            'balance' => $wallet->balance - $validated['amount']
        ]);

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdraw',
            'amount' => $validated['amount'],
            'description' => 'Withdraw ke ' . $validated['bank_name'] . ' (' . $validated['account_number'] . ')',
            'status' => 'pending',
            'reference_number' => 'WTH' . Str::random(12),
        ]);

        return back()->with('success', 'Permintaan withdraw berhasil dibuat! Status: Pending. Saldo Anda: Rp ' . number_format($wallet->balance - $validated['amount'], 0, ',', '.'));
    }

    // Payment / Pembayaran
    public function paymentIndex()
    {
        $wallet = Auth::user()->wallet;
        return view('transactions.payment', compact('wallet'));
    }

    public function paymentStore(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000|max:10000000',
            'payment_type' => 'required|in:electricity,water,internet,other',
            'description' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return back()->with('error', 'Wallet tidak ditemukan');
        }

        if ($wallet->balance < $validated['amount']) {
            return back()->with('error', 'Saldo tidak cukup!');
        }

        // Deduct from wallet
        $wallet->update([
            'balance' => $wallet->balance - $validated['amount']
        ]);

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => $validated['amount'],
            'description' => 'Pembayaran ' . $validated['payment_type'] . ': ' . $validated['description'],
            'status' => 'completed',
            'reference_number' => 'PAY' . Str::random(12),
        ]);

        return back()->with('success', 'Pembayaran berhasil! Saldo Anda: Rp ' . number_format($wallet->balance - $validated['amount'], 0, ',', '.'));
    }

    // Riwayat Transaksi
    public function history()
    {
        $user = Auth::user();

        // Eloquent transactions (wallet topups/transfers/withdraws)
        $tx1 = $user->transactions()->get()->map(function($t) {
            return (object) [
                'created_at' => $t->created_at,
                'type' => $t->type,
                'description' => $t->description,
                'amount' => $t->amount,
                'status' => $t->status ?? 'completed',
                'reference_number' => $t->reference_number ?? null,
            ];
        });

        // UMKM transactions (purchase/sale/internal etc)
        $umkm = \App\Models\UmkmTransaction::where('user_id', $user->id)->get()->map(function($u) {
            $desc = $u->meta['note'] ?? ($u->meta['from'] ?? ($u->meta['to'] ?? null));
            return (object) [
                'created_at' => $u->created_at,
                'type' => $u->type,
                'description' => $desc ?? $u->type,
                'amount' => $u->amount,
                'status' => 'completed',
                'reference_number' => 'UMKM-'.$u->id,
            ];
        });

        // Merge and sort by created_at desc
        $all = $tx1->merge($umkm)->sortByDesc('created_at')->values();

        // Manual pagination
        $perPage = 15;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $itemsForCurrentPage = $all->slice($offset, $perPage)->all();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $transactions = $paginator;

        return view('transactions.history', compact('transactions'));
    }
}
