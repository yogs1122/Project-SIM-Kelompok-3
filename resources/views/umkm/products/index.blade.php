@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Produk UMKM</h1>
        <a href="{{ route('umkm.products.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Tambah Produk</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($products as $product)
            <div class="p-4 bg-white shadow rounded">
                <h3 class="font-semibold">{{ $product->name }}</h3>
                <p class="text-sm text-gray-600">{{ \,Str::limit($product->description, 100) }}</p>
                <div class="mt-3 flex gap-2">
                    <a href="{{ route('umkm.products.edit', $product) }}" class="text-sm px-3 py-1 bg-gray-100 rounded">Edit</a>
                    <form method="POST" action="{{ route('umkm.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm px-3 py-1 bg-red-100 text-red-700 rounded">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-4 bg-white shadow rounded">Belum ada produk.</div>
        @endforelse
    </div>
</div>
@endsection
