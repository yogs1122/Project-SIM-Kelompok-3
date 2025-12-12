<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SmartFinanceRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSmartFinanceController extends Controller
{
    // List users with income/expense/net
    public function index(Request $request)
    {
        $users = User::select('users.id', 'users.name', 'users.email')
            ->leftJoin('transactions', 'users.id', '=', 'transactions.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get()
            ->map(function ($u) {
                $income = DB::table('transactions')->where('user_id', $u->id)->where('type', 'topup')->sum('amount');
                $expense = DB::table('transactions')->where('user_id', $u->id)->whereIn('type', ['payment','withdraw','transfer'])->sum('amount');
                $net = $income - $expense;
                $recCount = SmartFinanceRecommendation::where('user_id', $u->id)->count();
                return (object)[
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $net,
                    'recommendations' => $recCount,
                ];
            });

        return view('admin.smart_finance.index', compact('users'));
    }

    // Show detail for a user and recommendations
    public function show(User $user)
    {
        $transactions = $user->transactions()->orderByDesc('created_at')->get();
        $income = $transactions->where('type', 'topup')->sum('amount');
        $expense = $transactions->whereIn('type', ['payment','withdraw','transfer'])->sum('amount');
        $net = $income - $expense;

        $recommendations = SmartFinanceRecommendation::where('user_id', $user->id)->orderByDesc('created_at')->get();

        return view('admin.smart_finance.show', compact('user','transactions','income','expense','net','recommendations'));
    }

    // Store admin recommendation
    public function storeRecommendation(Request $request, User $user)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        SmartFinanceRecommendation::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'message' => $request->input('message'),
        ]);

        return back()->with('success', 'Rekomendasi berhasil dikirim ke user.');
    }
}
