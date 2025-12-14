<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UmkmWalletService;
use App\Models\User;

class WalletController extends Controller
{
    protected $service;

    public function __construct(UmkmWalletService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $user = auth()->user();
        return view('umkm.wallet', compact('user'));
    }

    public function history()
    {
        $user = auth()->user();
        $transactions = \App\Models\UmkmTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('umkm.wallet_history', compact('user', 'transactions'));
    }

    public function transferToUser(Request $request)
    {
        $data = $request->validate([
            'to_user_email' => 'required|email',
            'amount' => 'required|numeric|min:1000',
        ]);

        $from = auth()->user();
        $to = User::where('email', $data['to_user_email'])->firstOrFail();

        // Business rule: only saldo_pribadi can transfer to other users
        try {
            $tx = $this->service->transferToUser($from, $to, (float) $data['amount']);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

        return back()->with('success', 'Transfer berhasil');
    }

    public function internalTransfer(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $user = auth()->user();

        try {
            $this->service->transferStoreToPersonal($user, (float) $data['amount']);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

        return back()->with('success', 'Transfer internal berhasil');
    }

    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_account' => 'required|string',
        ]);

        $user = auth()->user();

        try {
            $this->service->withdrawFromStore($user, (float) $data['amount'], ['bank_account' => $data['bank_account']]);
        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }

        return back()->with('success', 'Permintaan penarikan diajukan');
    }
}
