<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTransactionController extends Controller
{
    /**
     * Display transaction analytics with user clustering
     */
    public function index(Request $request)
    {
        // Total transactions by type
        $transactionsByType = Transaction::groupBy('type')
            ->selectRaw('type, COUNT(*) as total, SUM(amount) as total_amount')
            ->get();

        // Users by transaction activity (clustering)
        $userActivity = User::with(['roles'])
            ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->selectRaw('users.id, users.name, users.email, users.created_at, COUNT(transactions.id) as transaction_count, SUM(transactions.amount) as total_spent')
            ->orderByDesc('transaction_count')
            ->paginate(20);

        // Transaction status breakdown
        $transactionsByStatus = Transaction::groupBy('status')
            ->selectRaw('status, COUNT(*) as total, SUM(amount) as total_amount')
            ->get();

        // High-value users (clustering by spending)
        $highValueUsers = User::leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->selectRaw('users.id, users.name, users.email, COUNT(transactions.id) as transaction_count, SUM(transactions.amount) as total_spent')
            ->havingRaw('COUNT(transactions.id) > 0')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with('user')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Monthly transaction trend
        $monthlyTrend = Transaction::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total_amount, COUNT(*) as total_transactions')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('year DESC, month DESC')
            ->limit(12)
            ->get();

        return view('admin.transactions.index', compact(
            'transactionsByType',
            'userActivity',
            'transactionsByStatus',
            'highValueUsers',
            'recentTransactions',
            'monthlyTrend'
        ));
    }

    /**
     * Show details for a specific user
     */
    public function userDetail(User $user)
    {
        $userTransactions = $user->transactions()
            ->orderByDesc('created_at')
            ->paginate(15);

        $statistics = [
            'total_transactions' => $user->transactions()->count(),
            'total_spent' => $user->transactions()->sum('amount'),
            'average_transaction' => $user->transactions()->avg('amount'),
            'subscription_days' => now()->diffInDays($user->created_at),
        ];

        return view('admin.transactions.user-detail', compact('user', 'userTransactions', 'statistics'));
    }

    /**
     * Top-up report: totals per user and monthly average per user
     */
    public function topUpReport(Request $request)
    {
        // Top-up totals per user
        $topUpByUser = DB::table('transactions')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.type', 'topup')
            ->selectRaw('users.id as user_id, users.name, users.email, SUM(transactions.amount) as total_topup, COUNT(transactions.id) as total_count')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_topup')
            ->limit(50)
            ->get();

        // Monthly totals and unique users (to compute average per user per month)
        $monthly = DB::table('transactions')
            ->where('type', 'topup')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total_amount, COUNT(DISTINCT user_id) as unique_users')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) ASC, MONTH(created_at) ASC')
            ->get();

        $labels = [];
        $monthlyAvg = [];
        foreach ($monthly as $m) {
            $labels[] = $m->month . '/' . $m->year;
            $avg = $m->unique_users > 0 ? ($m->total_amount / $m->unique_users) : 0;
            $monthlyAvg[] = round($avg, 2);
        }

        return view('admin.transactions.topups', compact('topUpByUser', 'monthly', 'labels', 'monthlyAvg'));
    }
}
