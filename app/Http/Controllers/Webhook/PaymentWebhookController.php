<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Services\UmkmWalletService;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Generic payment gateway webhook handler.
     * Expects JSON: { reference, amount, status }
     * Optional header X-Signature for HMAC-SHA256 verification using `services.payment.secret`.
     */
    public function handle(Request $request, UmkmWalletService $walletService)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Signature');
        $secret = config('services.payment.secret');

        if ($secret && $signature) {
            $hash = hash_hmac('sha256', $payload, $secret);
            if (!hash_equals($hash, $signature)) {
                Log::warning('Payment webhook invalid signature', ['sig' => $signature]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        $data = $request->validate([
            'reference' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $order = PurchaseOrder::where('reference', $data['reference'])->first();
        if (! $order) {
            Log::warning('Payment webhook: order not found', $data);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Idempotency: if already processed, return success
        if (in_array($order->status, ['paid', 'completed'])) {
            return response()->json(['message' => 'Already processed'], 200);
        }

        $status = strtolower($data['status']);
        if (! in_array($status, ['paid', 'success', 'completed'])) {
            // mark awaiting confirmation and keep payload for audit
            $order->update(['status' => 'awaiting_confirmation', 'meta' => array_merge((array) $order->meta, ['last_webhook' => $data])]);
            return response()->json(['message' => 'Payment not confirmed'], 200);
        }

        // Validate amount
        if ((float) $data['amount'] < (float) $order->amount) {
            Log::warning('Payment webhook amount mismatch', ['order' => $order->id, 'payload' => $data]);
            return response()->json(['message' => 'Amount less than order amount'], 400);
        }

        try {
            // mark paid, credit store, then complete
            $order->update(['status' => 'paid', 'meta' => array_merge((array) $order->meta, ['webhook' => $data])]);

            $walletService->creditStoreFromSale($order->merchant, (float) $order->amount, ['order_reference' => $order->reference, 'webhook' => $data]);

            $order->update(['status' => 'completed']);

            return response()->json(['message' => 'OK'], 200);
        } catch (\Exception $e) {
            Log::error('Payment webhook processing failed', ['error' => $e->getMessage(), 'order' => $order->id]);
            return response()->json(['message' => 'Processing error'], 500);
        }
    }
}
