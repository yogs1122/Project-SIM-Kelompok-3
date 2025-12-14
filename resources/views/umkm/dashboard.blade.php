@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Dashboard Pedagang UMKM</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('umkm.products.index') }}" class="block p-4 bg-white shadow rounded hover:shadow-md">
            <h2 class="font-semibold">Produk</h2>
            <p class="text-sm text-gray-600">Kelola produk UMKM Anda.</p>
        </a>

        <a href="{{ route('umkm.orders.index') }}" class="block p-4 bg-white shadow rounded hover:shadow-md">
            <h2 class="font-semibold">Pesanan</h2>
            <p class="text-sm text-gray-600">Lihat pesanan masuk dan kelola status.</p>
        </a>
    </div>
</div>
@endsection
