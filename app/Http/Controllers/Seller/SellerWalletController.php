<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SellerWalletService;
use Illuminate\Support\Facades\Log;

class SellerWalletController extends Controller
{
    protected SellerWalletService $service;

    public function __construct(SellerWalletService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    // GET /seller
    public function index()
    {
        $user = auth()->user();
        $summary = $this->service->getSummary($user->id);

        return response()->json([
            'success' => true,
            'data' => [
                'seller_id' => $user->id,
                'merchant_wallet_balance' => optional($user->merchantWallet)->balance ?? 0,
                'summary' => $summary,
            ]
        ]);
    }

    // GET /seller/transactions
    public function transactions(Request $request)
    {
        $user = auth()->user();
        $perPage = (int) $request->query('per_page', 15);
        $tx = $this->service->getTransactions($user->id, $perPage);

        return response()->json([
            'success' => true,
            'data' => $tx,
        ]);
    }

    // POST /seller/withdraw
    public function withdraw(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_account' => 'required|string',
            'bank_name' => 'nullable|string',
        ]);

        try {
            $res = $this->service->debitForWithdraw($user->id, (float) $data['amount'], $data['bank_account'], $data['bank_name'] ?? null, $user->id);
            return response()->json(['success' => true, 'message' => 'Withdraw request created', 'withdraw' => $res['withdraw']]);
        } catch (\Exception $e) {
            Log::error('Seller withdraw error: '.$e->getMessage(), ['user_id' => $user->id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
