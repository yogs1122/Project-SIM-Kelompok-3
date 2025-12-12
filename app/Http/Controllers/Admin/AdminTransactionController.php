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
        $from = $request->input('from');
        $to = $request->input('to');

        // Top-up totals per user
        $topUpQuery = DB::table('transactions')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.type', 'topup')
            ->selectRaw('users.id as user_id, users.name, users.email, SUM(transactions.amount) as total_topup, COUNT(transactions.id) as total_count')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_topup');

        if ($from && $to) {
            $topUpQuery->whereBetween('transactions.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $topUpByUser = $topUpQuery->limit(50)->get();

        // Monthly totals and unique users (to compute average per user per month)
        $monthlyQuery = DB::table('transactions')
            ->where('type', 'topup')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total_amount, COUNT(DISTINCT user_id) as unique_users')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) ASC, MONTH(created_at) ASC');

        if ($from && $to) {
            $monthlyQuery->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $monthly = $monthlyQuery->get();

        $labels = [];
        $monthlyAvg = [];
        foreach ($monthly as $m) {
            $labels[] = $m->month . '/' . $m->year;
            $avg = $m->unique_users > 0 ? ($m->total_amount / $m->unique_users) : 0;
            $monthlyAvg[] = round($avg, 2);
        }

        // CSV export
        if ($request->input('export') === 'csv') {
            $filename = 'topups_report_' . now()->format('Ymd_His') . '.csv';
            $handle = fopen('php://memory', 'w');
            fputcsv($handle, ['Nama', 'Email', 'Total Top-up', 'Jumlah Transaksi']);
            foreach ($topUpByUser as $row) {
                fputcsv($handle, [$row->name, $row->email, $row->total_topup, $row->total_count]);
            }
            fseek($handle, 0);
            return response(stream_get_contents($handle), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return view('admin.transactions.topups', compact('topUpByUser', 'monthly', 'labels', 'monthlyAvg'));
    }

    /**
     * Transfer report: sent and received per user
     */
    public function transferReport(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        // Sent transfers (description format: 'Transfer ke {name} ...')
        $sentQuery = DB::table('transactions')
            ->where('type', 'transfer')
            ->where('description', 'like', 'Transfer ke %')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->selectRaw('users.id as user_id, users.name, users.email, COUNT(transactions.id) as sent_count, SUM(transactions.amount) as sent_total')
            ->groupBy('users.id', 'users.name', 'users.email');

        // Received transfers (description format: 'Transfer dari {name}')
        $receivedQuery = DB::table('transactions')
            ->where('type', 'transfer')
            ->where('description', 'like', 'Transfer dari %')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->selectRaw('users.id as user_id, users.name, users.email, COUNT(transactions.id) as received_count, SUM(transactions.amount) as received_total')
            ->groupBy('users.id', 'users.name', 'users.email');

        if ($from && $to) {
            $sentQuery->whereBetween('transactions.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            $receivedQuery->whereBetween('transactions.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        }

        $sent = $sentQuery->get()->keyBy('user_id');
        $received = $receivedQuery->get()->keyBy('user_id');

        // Merge into combined per-user array
        $users = collect([]);
        $allIds = $sent->keys()->merge($received->keys())->unique();
        foreach ($allIds as $id) {
            $s = $sent->get($id);
            $r = $received->get($id);
            $users->push((object)[
                'user_id' => $id,
                'name' => $s->name ?? $r->name ?? '—',
                'email' => $s->email ?? $r->email ?? '—',
                'sent_count' => $s->sent_count ?? 0,
                'sent_total' => $s->sent_total ?? 0,
                'received_count' => $r->received_count ?? 0,
                'received_total' => $r->received_total ?? 0,
            ]);
        }

        // Sort by total activity
        $users = $users->sortByDesc(function ($u) {
            return ($u->sent_total ?? 0) + ($u->received_total ?? 0);
        })->values();

        if ($request->input('export') === 'csv') {
            $filename = 'transfers_report_' . now()->format('Ymd_His') . '.csv';
            $handle = fopen('php://memory', 'w');
            fputcsv($handle, ['Nama', 'Email', 'Sent Count', 'Sent Total', 'Received Count', 'Received Total']);
            foreach ($users as $row) {
                fputcsv($handle, [$row->name, $row->email, $row->sent_count, $row->sent_total, $row->received_count, $row->received_total]);
            }
            fseek($handle, 0);
            return response(stream_get_contents($handle), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return view('admin.transactions.transfers', compact('users'));
    }

    /**
     * Weekly transaction history (last 7 days)
     */
    public function weeklyHistory(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $since = $from && $to ? null : now()->subDays(7);

        $txQuery = DB::table('transactions')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->select('transactions.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('transactions.created_at');

        if ($from && $to) {
            $txQuery->whereBetween('transactions.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } else {
            $txQuery->where('transactions.created_at', '>=', $since);
        }

        $transactions = $txQuery->get();

        // Group by user for summary
        $grouped = $transactions->groupBy('user_id')->map(function ($group) {
            return [
                'user_name' => $group->first()->user_name,
                'user_email' => $group->first()->user_email,
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'transactions' => $group,
            ];
        });

        if ($request->input('export') === 'csv') {
            $filename = 'weekly_transactions_' . now()->format('Ymd_His') . '.csv';
            $handle = fopen('php://memory', 'w');
            fputcsv($handle, ['Waktu', 'User', 'Email', 'Tipe', 'Jumlah', 'Status', 'Referensi']);
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->created_at,
                    $t->user_name,
                    $t->user_email,
                    $t->type,
                    $t->amount,
                    $t->status,
                    $t->reference_number,
                ]);
            }
            fseek($handle, 0);
            return response(stream_get_contents($handle), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return view('admin.transactions.weekly', compact('transactions', 'grouped'));
    }
}
