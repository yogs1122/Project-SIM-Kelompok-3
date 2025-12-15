@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-10">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Seller Dashboard</h1>

        <p class="text-sm text-gray-600 mb-4">Halaman ini khusus untuk akun dengan peran <strong>seller</strong> (merchant). Digunakan untuk pengujian tampilan terpisah dari dashboard pengguna biasa.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 border rounded">
                <h3 class="font-semibold">Merchant Wallet</h3>
                <div class="mt-3 text-lg">
                    @if($merchantWallet)
                        <div>Saldo: <strong>Rp {{ number_format($merchantWallet->balance, 0, ',', '.') }}</strong></div>
                        <div class="text-sm text-gray-500">Status: {{ ucfirst($merchantWallet->status ?? 'active') }}</div>
                    @else
                        <div class="text-gray-500">Merchant wallet belum tersedia untuk akun ini.</div>
                    @endif
                </div>
            </div>

            <div class="p-4 border rounded">
                <h3 class="font-semibold">Quick Links</h3>
                <div class="mt-3 space-y-2">
                    <a href="/seller" class="inline-block px-3 py-2 bg-blue-600 text-white rounded">Seller Wallet</a>
                    <a href="/seller/transactions" class="inline-block px-3 py-2 bg-gray-200 rounded">Transactions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
