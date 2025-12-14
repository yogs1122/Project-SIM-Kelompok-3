@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-xl font-semibold mb-2">Order Pembelian Saldo</h1>
    <p>Reference: <strong>{{ $order->reference }}</strong></p>
    <p>Merchant: {{ $order->merchant->name }} ({{ $order->merchant->email }})</p>
    <p>Jumlah: Rp {{ number_format($order->amount,2,',','.') }}</p>
    <p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>

    <div class="mt-4">
        <p class="mb-2">Silakan lakukan pembayaran ke rekening merchant kemudian klik tombol WA untuk menghubungi merchant. Sertakan reference di pesan:</p>
        <a href="https://wa.me/?text={{ urlencode('Saya melakukan pembayaran untuk reference: '.$order->reference) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded">Hubungi via WA</a>
    </div>

    @if($order->status === 'pending')
    <div class="mt-6">
        <form action="{{ route('purchase_orders.upload_proof', $order) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block text-sm">Unggah bukti pembayaran (opsional)</label>
                <input type="file" name="proof" accept="image/*" class="mt-2">
            </div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Kirim Bukti</button>
        </form>
    </div>
    @endif
</div>
@endsection
