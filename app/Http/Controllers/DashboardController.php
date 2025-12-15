<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Detect seller/merchant roles (supports 'seller', 'merchant', or legacy 'umkm')
        $isSeller = $user->roles()->whereIn('name', ['seller', 'merchant', 'umkm'])->exists();

        // If seller, provide merchant wallet data to the view for conditional rendering
        $merchantWallet = null;
        if ($isSeller) {
            $merchantWallet = $user->merchantWallet;
        }

        $wallet = $user->wallet;
        $transactions = $user->transactions()->latest()->paginate(10);
        $recentTransactions = $user->transactions()->latest()->limit(5)->get();

        // 🔹 Hitung Income & Expense user ini saja
        $income = $user->transactions()->where('type', 'income')->sum('amount');
        $expense = $user->transactions()->where('type', 'expense')->sum('amount');

        // 🔹 Grafik bulan (Jan–Dec)
        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M'));

        $chartIncome = [];
        $chartExpense = [];

        foreach (range(1, 12) as $month) {
            $chartIncome[] = $user->transactions()->where('type', 'income')->whereMonth('created_at', $month)->sum('amount');
            $chartExpense[] = $user->transactions()->where('type', 'expense')->whereMonth('created_at', $month)->sum('amount');
        }

        // 🔹 Tips Dinamis
        $tips = [];
        $net = $income - $expense;

        if ($expense > $income) {
            $tips[] = "⚠️ Pengeluaran melebihi pemasukan, kurangi konsumsi tidak perlu!";
        }

        if ($net < 100000) {
            $tips[] = "💡 Saldo kecil, coba sisihkan dulu baru belanja!";
        }

        if ($income > $expense && $net >= 100000) {
            $tips[] = "🎯 Keuangan cukup sehat, pertahankan dan mulai tabung untuk tujuan jangka panjang!";
        }

        if (empty($tips)) {
            $tips[] = "📌 Awali kebiasaan keuangan baik dengan mencatat setiap transaksi!";
        }

        return view('dashboard', compact(
            'wallet',
            'transactions',
            'recentTransactions',
            'income',
            'expense',
            'months',
            'chartIncome',
            'chartExpense',
            'tips',
            'isSeller',
            'merchantWallet'
        ));
    }
}
