@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-xl font-semibold mb-4">Tambah Produk</h1>

    <form action="{{ route('umkm.products.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="block text-sm font-medium">Nama Produk</label>
            <input name="name" class="mt-1 block w-full border-gray-200 rounded" required>
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium">Deskripsi</label>
            <textarea name="description" class="mt-1 block w-full border-gray-200 rounded"></textarea>
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium">Harga</label>
            <input name="price" type="number" step="0.01" class="mt-1 block w-full border-gray-200 rounded">
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
            <a href="{{ route('umkm.products.index') }}" class="px-3 py-2 bg-gray-200 rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
