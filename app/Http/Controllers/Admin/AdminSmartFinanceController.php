<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SmartFinanceRecommendation;
use App\Models\SmartFinanceTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SmartFinanceRecommendationMail;

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
        $templates = SmartFinanceTemplate::orderByDesc('updated_at')->get();

        return view('admin.smart_finance.show', compact('user','transactions','income','expense','net','recommendations','templates'));
    }

    // Store admin recommendation
    public function storeRecommendation(Request $request, User $user)
    {
        $request->validate(['message' => 'required_without:template_id|string|max:2000','template_id' => 'nullable|integer|exists:smart_finance_templates,id']);

        // compute some user stats for placeholder rendering
        $income = DB::table('transactions')->where('user_id', $user->id)->where('type', 'topup')->sum('amount');
        $expense = DB::table('transactions')->where('user_id', $user->id)->whereIn('type', ['payment','withdraw','transfer'])->sum('amount');
        $net = $income - $expense;

        // If template selected, use its body as raw message
        if ($request->filled('template_id')) {
            $tpl = SmartFinanceTemplate::find($request->input('template_id'));
            $raw = $tpl ? $tpl->body : $request->input('message');
        } else {
            $raw = $request->input('message');
        }
        $rendered = str_replace([
            '{name}',
            '{net_balance}',
            '{monthly_income}',
            '{monthly_expense}',
            '{date}'
        ], [
            $user->name,
            number_format($net,0,',','.'),
            number_format($income,0,',','.'),
            number_format($expense,0,',','.'),
            now()->toDateString()
        ], $raw);

        $rec = SmartFinanceRecommendation::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'message' => $raw,
            'rendered_message' => $rendered,
            'is_read' => false,
        ]);

        // Attempt to send email notification (queued). Falls back to sync if queue not configured.
        try {
            Mail::to($user->email)->queue(new SmartFinanceRecommendationMail($rec));
        } catch (\Throwable $e) {
            // Don't block admin UX; log and continue
            logger()->warning('Failed to queue SmartFinanceRecommendation email: '.$e->getMessage());
        }

        return back()->with('success', 'Rekomendasi berhasil dikirim ke user.');
    }
}
