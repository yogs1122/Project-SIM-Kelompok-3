@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-semibold mb-4">Upgrade ke Akun Pedagang UMKM</h1>

        @if(session('error'))
            <div class="mb-4 text-red-700">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="mb-4 text-green-700">{{ session('success') }}</div>
        @endif

        <p class="mb-4">Untuk dapat membuat posting penjualan di Forum Jual Beli, akun Anda harus terdaftar sebagai <strong>Pedagang UMKM</strong>. Dengan melakukan upgrade, Anda akan mendapatkan akses untuk membuat produk, mengelola posting penjualan, dan fitur pedagang lainnya.</p>

        <form method="POST" action="{{ route('umkm.upgrade.post') }}">
            @csrf
            <p class="mb-4">Klik tombol di bawah untuk mengupgrade akun Anda menjadi Pedagang UMKM.</p>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Upgrade ke UMKM</button>
            <a href="{{ route('profile.edit') }}" class="ml-4 text-sm text-gray-600">Batal</a>
        </form>

        <hr class="my-6">
        <p class="text-sm text-gray-600">Catatan: Proses upgrade ini bersifat langsung. Jika Anda membutuhkan verifikasi tambahan, sistem harus diperluas dengan proses approval.</p>
    </div>
</div>
@endsection
