@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-xl font-semibold mb-2">{{ $product->name }}</h1>
    <p class="text-gray-600">{{ $product->description }}</p>
    <p class="mt-3 font-semibold">Harga: {{ $product->price ?? '-' }}</p>
    <div class="mt-4">
        <a href="{{ route('umkm.products.edit', $product) }}" class="px-3 py-2 bg-gray-100 rounded">Edit</a>
    </div>
</div>
@endsection
