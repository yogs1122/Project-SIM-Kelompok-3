<?php

namespace App\Http\Controllers;


use App\Models\Transaction;
use App\Models\SmartFinanceRecommendation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SmartFinanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        if ($transactions->count() < 2) {
            $tips = ["Tambah transaksi untuk hasil analisis yang lebih akurat 🔍"];

            // Load latest admin recommendations even if user has few transactions
            $recommendations = SmartFinanceRecommendation::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            return view('smart_finance.index', compact('tips','recommendations'))
                ->with([
                    'income' => 0,
                    'expense' => 0,
                    'wallet' => 0,
                    'latestTransactions' => collect([]),
                    'regressionPoints' => collect([]),
                    'regressionModel' => null,
                    'forecastPoints' => collect([]),
                    'months' => collect([]),
                    'chartIncome' => collect([]),
                    'chartExpense' => collect([]),
                    'dailySpend' => collect([]),
                    'monthlyExpenseMean' => 0,
                    'monthlyExpenseStd' => 0,
                    'cv' => 0,
                    'avgTx' => 0,
                    'monthlyStd' => collect([]),
                ]);
        }

        // Hitung pemasukan, pengeluaran, saldo
        $income = $transactions->where('type', 'topup')->sum('amount');
        $expense = $transactions->whereIn('type', ['payment', 'transfer', 'withdraw'])->sum('amount');
        $wallet = $income - $expense;

        $latestTransactions = $transactions->sortByDesc('created_at')->take(5);

        list($regressionPoints, $regressionModel) = $this->calculateLinearRegression($transactions);
        $forecastPoints = $this->generateForecast($regressionModel);

        // Generate monthly chart data
        list($months, $chartIncome, $chartExpense) = $this->generateMonthlyChartData($transactions);

        // Generate daily spend data
        $dailySpend = $this->generateDailySpendData($transactions);

        // Volatility / statistics for budgeting tips
        $monthlyExpenseMean = $this->arrayMean($chartExpense);
        $monthlyExpenseStd = $this->arrayStdDev($chartExpense);
        $cv = $monthlyExpenseMean > 0 ? ($monthlyExpenseStd / $monthlyExpenseMean) : 0; // coefficient of variation
        $avgTx = $transactions->avg('amount') ?? 0;

        // Generate base tips and then append volatility tip if needed
        $tips = $this->generateSmartTips($transactions);

        
            // Compute 3-month rolling stddev for volatility chart
            $monthlyStd = [];
            $n = count($chartExpense);
            for ($i = 0; $i < $n; $i++) {
                $start = max(0, $i - 2);
                $window = array_slice($chartExpense, $start, $i - $start + 1);
                $monthlyStd[] = $this->arrayStdDev($window);
            }
            // normalize to Collection for safer operations in view
            $monthlyStd = collect($monthlyStd);
        if ($cv > 0.4) {
            $tips[] = "Pengeluaran bulanan sangat fluktuatif (CV=" . round($cv,2) . "). Pertimbangkan membuat anggaran tetap dan menyiapkan dana cadangan.";
        }

        // Load latest admin recommendations for this user
        $recommendations = SmartFinanceRecommendation::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('smart_finance.index', compact(
            'income',
            'expense',
            'wallet',
            'latestTransactions',
            'tips',
            'recommendations',
            'regressionPoints',
            'regressionModel',
            'forecastPoints',
            'months',
            'chartIncome',
            'chartExpense',
            'dailySpend',
            'monthlyExpenseMean',
            'monthlyExpenseStd',
            'cv',
            'avgTx',
            'monthlyStd'
        ));
    }

    // Mark a recommendation as read by the owner
    public function markRecommendationRead(Request $request, SmartFinanceRecommendation $rec)
    {
        $user = Auth::user();
        if ($rec->user_id !== $user->id) {
            abort(403);
        }

        $rec->is_read = true;
        $rec->save();

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'Rekomendasi ditandai sudah dibaca.');
    }

    private function calculateLinearRegression($transactions)
    {
        $x = [];
        $y = [];

        foreach ($transactions as $index => $trx) {
            $x[] = $index + 1;
            $y[] = $trx->amount;
        }

        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn ($a, $b) => $a * $b, $x, $y));
        $sumX2 = array_sum(array_map(fn ($a) => $a * $a, $x));

        $slope = (($n * $sumXY) - ($sumX * $sumY)) / (($n * $sumX2) - ($sumX ** 2));
        $intercept = ($sumY - ($slope * $sumX)) / $n;

        $points = [];
        foreach ($x as $xi) {
            $points[] = [
                "x" => $xi,
                "y" => round($slope * $xi + $intercept)
            ];
        }

        return [$points, ["slope" => $slope, "intercept" => $intercept]];
    }

    private function generateForecast($model)
    {
        $forecast = [];
        for ($i = 1; $i <= 7; $i++) {
            $forecast[] = [
                "day" => "Hari ke-$i",
                "value" => round($model["slope"] * (count($forecast) + 10) + $model["intercept"])
            ];
        }
        return $forecast;
    }

    private function generateSmartTips($transactions)
    {
        $avgExpense = $transactions->whereIn('type', ['payment','withdraw','transfer'])->avg('amount');
        $totalExpense = $transactions->whereIn('type', ['payment','withdraw','transfer'])->sum('amount');

        $tips = [];

        if ($avgExpense > 50000) {
            $tips[] = "Rata-rata pengeluaranmu cukup besar. Coba kurangi sedikit ya! 💸";
        }

        if ($totalExpense > 1000000) {
            $tips[] = "Pengeluaranmu sudah diatas 1 juta. Yuk mulai budgeting! 📊";
        }

        if (count($tips) === 0) {
            $tips[] = "Keuanganmu sejauh ini aman! Tetap pertahankan 💪";
        }

        return $tips;
    }

    private function generateMonthlyChartData($transactions)
    {
        // Prepare last 12 months labels (from oldest to newest)
        $now = now();
        $monthKeys = [];
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $key = $m->format('Y-m');
            $monthKeys[$key] = ['income' => 0, 'expense' => 0];
            $months[] = $m->format('M Y');
        }

        // Aggregate transactions into the 12-month buckets
        foreach ($transactions as $trx) {
            $key = $trx->created_at->format('Y-m');
            if (!array_key_exists($key, $monthKeys)) continue; // ignore older/newer than 12 months

            if ($trx->type === 'topup') {
                $monthKeys[$key]['income'] += $trx->amount;
            } else {
                $monthKeys[$key]['expense'] += $trx->amount;
            }
        }

        $chartIncome = array_map(fn($v) => $v['income'], $monthKeys);
        $chartExpense = array_map(fn($v) => $v['expense'], $monthKeys);

        return [$months, array_values($chartIncome), array_values($chartExpense)];
    }

    private function generateDailySpendData($transactions)
    {
        $dailyData = [];

        foreach ($transactions as $trx) {
            $amount = $trx->type === 'topup' ? $trx->amount : -$trx->amount;
            $dailyData[] = ['x' => count($dailyData) + 1, 'y' => $amount];
        }

        return collect($dailyData);
    }

    // Utility: calculate mean of numeric array
    private function arrayMean(array $arr)
    {
        if (count($arr) === 0) return 0;
        return array_sum($arr) / count($arr);
    }

    // Utility: population standard deviation
    private function arrayStdDev(array $arr)
    {
        $n = count($arr);
        if ($n === 0) return 0;
        $mean = $this->arrayMean($arr);
        $sum = 0;
        foreach ($arr as $v) {
            $sum += ($v - $mean) ** 2;
        }
        return sqrt($sum / $n);
    }
}
