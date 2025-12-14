@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Order Pembelian dari Pembeli</h1>

    <div class="bg-white shadow rounded">
        <table class="w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Reference</th>
                    <th class="p-3 text-left">Pembeli</th>
                    <th class="p-3 text-right">Jumlah</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-t">
                    <td class="p-3">{{ $order->reference }}</td>
                    <td class="p-3">{{ $order->buyer->name }} / {{ $order->buyer->email }}</td>
                    <td class="p-3 text-right">{{ number_format($order->amount,2,',','.') }}</td>
                    <td class="p-3">{{ $order->status }}</td>
                    <td class="p-3">
                        @if(in_array($order->status, ['pending','awaiting_confirmation']))
                            <form action="{{ route('umkm.purchase_orders.merchant_confirm', $order) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran dan kredit saldo toko?');">
                                @csrf
                                <button class="px-3 py-1 bg-green-600 text-white rounded">Tandai Terbayar</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="p-4" colspan="5">Belum ada order.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
