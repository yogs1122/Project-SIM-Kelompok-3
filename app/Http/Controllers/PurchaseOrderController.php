<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\UmkmWalletService;

class PurchaseOrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'merchant_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000'
        ]);

        $reference = strtoupper(Str::random(8));

        $order = PurchaseOrder::create([
            'reference' => $reference,
            'buyer_id' => auth()->id(),
            'merchant_id' => $data['merchant_id'],
            'amount' => $data['amount'],
            'status' => 'pending',
            'expires_at' => now()->addHours(6),
        ]);

        return redirect()->route('purchase_orders.show', $order->reference);
    }

    public function show($reference)
    {
        $order = PurchaseOrder::where('reference', $reference)->firstOrFail();
        return view('purchase_orders.show', compact('order'));
    }

    public function uploadProof(Request $request, PurchaseOrder $order)
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('proof')->store('purchase_proofs', 'public');
        $order->update(['proof_path' => $path, 'status' => 'awaiting_confirmation']);

        return back()->with('success', 'Bukti pembayaran dikirim, menunggu verifikasi merchant.');
    }

    // Merchant views pending orders
    public function merchantIndex()
    {
        $user = auth()->user();
        $orders = PurchaseOrder::where('merchant_id', $user->id)->latest()->paginate(20);
        return view('umkm.purchase_orders.index', compact('orders'));
    }

    // Merchant confirms payment -> credit store balance
    public function merchantConfirm(PurchaseOrder $order, UmkmWalletService $walletService)
    {
        $this->authorize('manage', $order);

        if ($order->status === 'completed' || $order->status === 'paid') {
            return back()->with('info', 'Order sudah terkonfirmasi.');
        }

        // mark as paid and credit store
        $order->update(['status' => 'paid']);

        $walletService->creditStoreFromSale($order->merchant, (float) $order->amount, ['order_reference' => $order->reference]);

        $order->update(['status' => 'completed']);

        return back()->with('success', 'Order ditandai terbayar dan saldo toko dikreditkan.');
    }
}
