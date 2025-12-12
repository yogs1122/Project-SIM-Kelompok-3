<?php

namespace App\Http\Controllers;

use App\Models\SavingPlan;
use App\Models\SavingTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SavingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get all saving plans for the user
        $plans = SavingPlan::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $stats = [
            'total_active_plans' => $plans->where('status', 'active')->count(),
            'total_completed_plans' => $plans->where('status', 'completed')->count(),
            'total_savings' => $plans->where('status', 'active')->sum('current_amount'),
            'total_targets' => $plans->where('status', 'active')->sum('target_amount'),
            'overall_progress' => $plans->where('status', 'active')->sum('target_amount') > 0 
                ? round(($plans->where('status', 'active')->sum('current_amount') / $plans->where('status', 'active')->sum('target_amount')) * 100, 2)
                : 0,
        ];

        return view('savings.index', compact('plans', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = [
            'electronics' => 'Elektronik',
            'vehicle' => 'Kendaraan',
            'property' => 'Properti',
            'education' => 'Pendidikan',
            'vacation' => 'Liburan',
            'emergency' => 'Dana Darurat',
            'other' => 'Lainnya'
        ];

        return view('savings.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:10000',
            'target_date' => 'nullable|date|after:today',
            'category' => 'nullable|string|max:50',
        ]);

        try {
            $plan = SavingPlan::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'target_amount' => $validated['target_amount'],
                'current_amount' => 0,
                'target_date' => $validated['target_date'] ?? null,
                'category' => $validated['category'] ?? 'other',
                'status' => 'active',
            ]);

            return redirect()->route('savings.index')
                ->with('success', 'Rencana tabungan berhasil dibuat!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat rencana tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $plan = SavingPlan::where('user_id', Auth::id())
            ->findOrFail($id);

        // Get recent transactions
        $transactions = SavingTransaction::where('saving_plan_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Calculate stats
        $stats = [
            'progress_percentage' => $plan->target_amount > 0 
                ? round(($plan->current_amount / $plan->target_amount) * 100, 2)
                : 0,
            'remaining_amount' => max(0, $plan->target_amount - $plan->current_amount),
            'days_remaining' => $plan->target_date 
                ? Carbon::parse($plan->target_date)->diffInDays(Carbon::now())
                : null,
        ];

        return view('savings.show', compact('plan', 'transactions', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plan = SavingPlan::where('user_id', Auth::id())
            ->findOrFail($id);

        $categories = [
            'electronics' => 'Elektronik',
            'vehicle' => 'Kendaraan',
            'property' => 'Properti',
            'education' => 'Pendidikan',
            'vacation' => 'Liburan',
            'emergency' => 'Dana Darurat',
            'other' => 'Lainnya'
        ];

        return view('savings.edit', compact('plan', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $plan = SavingPlan::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:10000',
            'target_date' => 'nullable|date|after:today',
            'category' => 'nullable|string|max:50',
            'status' => 'required|in:active,completed',
        ]);

        try {
            // Auto-complete jika status completed tapi belum mencapai target
            if ($validated['status'] == 'completed' && $plan->current_amount < $validated['target_amount']) {
                $plan->current_amount = $validated['target_amount'];
            }

            $plan->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'target_amount' => $validated['target_amount'],
                'target_date' => $validated['target_date'] ?? null,
                'category' => $validated['category'] ?? 'other',
                'status' => $validated['status'],
                'current_amount' => $plan->current_amount,
            ]);

            return redirect()->route('savings.show', $plan->id)
                ->with('success', 'Rencana tabungan berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui rencana tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = SavingPlan::where('user_id', Auth::id())
            ->findOrFail($id);

        try {
            // Hapus semua transaksi terkait
            SavingTransaction::where('saving_plan_id', $id)->delete();
            
            // Hapus plan
            $plan->delete();
            
            return redirect()->route('savings.index')
                ->with('success', 'Rencana tabungan berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus rencana tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Add funds to a saving plan
     */
    public function addFunds(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        $plan = SavingPlan::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        // Get user
        $user = Auth::user();
        
        // Check if user has wallet and sufficient balance
        if (!$user->wallet || $user->wallet->balance < $validated['amount']) {
            return redirect()->back()
                ->with('error', 'Saldo tidak mencukupi!');
        }

        DB::beginTransaction();
        
        try {
            // Deduct from wallet
            $user->wallet->balance -= $validated['amount'];
            $user->wallet->save();
            
            // Add to saving plan
            $plan->current_amount += $validated['amount'];
            $plan->save();

            // Create transaction record
            SavingTransaction::create([
                'user_id' => Auth::id(),
                'saving_plan_id' => $plan->id,
                'amount' => $validated['amount'],
                'type' => 'deposit',
                'description' => $validated['description'] ?? 'Menambah tabungan',
            ]);

            DB::commit();

            return redirect()->route('savings.show', $plan->id)
                ->with('success', 'Berhasil menambah tabungan sebesar Rp ' . number_format($validated['amount'], 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal menambah tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Get transactions for a specific plan
     */
    public function transactions($id)
    {
        $plan = SavingPlan::where('user_id', Auth::id())
            ->findOrFail($id);

        $transactions = SavingTransaction::where('saving_plan_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('savings.transactions', compact('transactions', 'plan'));
    }

    /**
     * Get user's saving statistics
     */
    public function statistics()
    {
        $userId = Auth::id();
        
        // Get all plans
        $plans = SavingPlan::where('user_id', $userId)->get();
        
        // Calculate stats
        $stats = [
            'total_plans' => $plans->count(),
            'active_plans' => $plans->where('status', 'active')->count(),
            'completed_plans' => $plans->where('status', 'completed')->count(),
            'total_saved' => $plans->sum('current_amount'),
            'total_target' => $plans->sum('target_amount'),
            'overall_progress' => $plans->sum('target_amount') > 0 
                ? round(($plans->sum('current_amount') / $plans->sum('target_amount')) * 100, 2)
                : 0,
        ];

        // Category breakdown
        $categories = SavingPlan::where('user_id', $userId)
            ->select('category', 
                DB::raw('COUNT(*) as count'), 
                DB::raw('SUM(target_amount) as total_target'), 
                DB::raw('SUM(current_amount) as total_saved'))
            ->groupBy('category')
            ->get();

        return view('savings.statistics', compact('stats', 'categories'));
    }

    /**
     * Complete a saving plan manually
     */
    public function complete($id)
    {
        $plan = SavingPlan::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        try {
            $plan->update([
                'status' => 'completed',
                'current_amount' => $plan->target_amount
            ]);

            return redirect()->route('savings.show', $plan->id)
                ->with('success', 'Rencana tabungan telah diselesaikan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyelesaikan rencana tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Quick add funds with predefined amount
     */
    public function quickAddFunds(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $plan = SavingPlan::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        $user = Auth::user();
        
        if (!$user->wallet || $user->wallet->balance < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Deduct from wallet
            $user->wallet->balance -= $validated['amount'];
            $user->wallet->save();
            
            // Add to saving plan
            $plan->current_amount += $validated['amount'];
            $plan->save();

            // Create transaction
            SavingTransaction::create([
                'user_id' => Auth::id(),
                'saving_plan_id' => $plan->id,
                'amount' => $validated['amount'],
                'type' => 'deposit',
                'description' => 'Quick add funds',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambah tabungan',
                'new_plan_amount' => $plan->current_amount,
                'new_balance' => $user->wallet->balance,
                'progress' => round(($plan->current_amount / $plan->target_amount) * 100, 2)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah tabungan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw funds from saving plan (emergency)
     */
    public function withdrawFunds(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'reason' => 'required|string|max:255',
        ]);

        $plan = SavingPlan::where('user_id', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        if ($plan->current_amount < $validated['amount']) {
            return redirect()->back()
                ->with('error', 'Saldo tabungan tidak mencukupi!');
        }

        DB::beginTransaction();
        
        try {
            // Deduct from saving plan
            $plan->current_amount -= $validated['amount'];
            $plan->save();

            // Add to wallet
            $user = Auth::user();
            $user->wallet->balance += $validated['amount'];
            $user->wallet->save();

            // Create transaction record
            SavingTransaction::create([
                'user_id' => Auth::id(),
                'saving_plan_id' => $plan->id,
                'amount' => $validated['amount'],
                'type' => 'withdrawal',
                'description' => $validated['reason'],
            ]);

            DB::commit();

            return redirect()->route('savings.show', $plan->id)
                ->with('success', 'Berhasil menarik dana sebesar Rp ' . number_format($validated['amount'], 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Gagal menarik dana: ' . $e->getMessage());
        }
    }
}