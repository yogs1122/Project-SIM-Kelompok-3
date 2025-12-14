@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Dompet UMKM</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="p-4 bg-white shadow rounded">
            <h2 class="font-semibold">Saldo Pribadi</h2>
            <p class="text-xl">Rp {{ number_format($user->saldo_pribadi, 2, ',', '.') }}</p>
            <p class="text-sm text-gray-600">Bisa digunakan untuk transfer ke pengguna lain.</p>
        </div>

        <div class="p-4 bg-white shadow rounded">
            <h2 class="font-semibold">Saldo Toko</h2>
            <p class="text-xl">Rp {{ number_format($user->saldo_toko, 2, ',', '.') }}</p>
            <p class="text-sm text-gray-600">Hanya untuk menerima pembayaran, tarik dana, atau transfer internal ke saldo pribadi.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-white shadow rounded">
            <h3 class="font-semibold mb-2">Transfer ke pengguna (dari Saldo Pribadi)</h3>
            <form action="{{ route('umkm.wallet.transfer') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="text-sm">Email penerima</label>
                    <input name="to_user_email" type="email" required class="mt-1 block w-full border-gray-200 rounded">
                </div>
                <div class="mb-2">
                    <label class="text-sm">Jumlah (Rp)</label>
                    <input name="amount" type="number" step="0.01" required class="mt-1 block w-full border-gray-200 rounded">
                </div>
                <button class="px-3 py-2 bg-indigo-600 text-white rounded">Kirim</button>
            </form>
        </div>

        <div class="p-4 bg-white shadow rounded">
            <h3 class="font-semibold mb-2">Transfer internal (Saldo Toko → Saldo Pribadi)</h3>
            <form action="{{ route('umkm.wallet.internal_transfer') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="text-sm">Jumlah (Rp)</label>
                    <input name="amount" type="number" step="0.01" required class="mt-1 block w-full border-gray-200 rounded">
                </div>
                <button class="px-3 py-2 bg-indigo-600 text-white rounded">Transfer Internal</button>
            </form>
        </div>

        <div class="p-4 bg-white shadow rounded">
            <h3 class="font-semibold mb-2">Tarik Dana dari Saldo Toko</h3>
            <form action="{{ route('umkm.wallet.withdraw') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="text-sm">Jumlah (Rp)</label>
                    <input name="amount" type="number" step="0.01" required class="mt-1 block w-full border-gray-200 rounded">
                </div>
                <div class="mb-2">
                    <label class="text-sm">Rekening Tujuan</label>
                    <input name="bank_account" type="text" required class="mt-1 block w-full border-gray-200 rounded">
                </div>
                <button class="px-3 py-2 bg-indigo-600 text-white rounded">Tarik Dana</button>
            </form>
        </div>
    </div>
</div>
@endsection
