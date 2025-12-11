@extends('layouts.app')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Kelola UMKM</h1>
    <p class="text-gray-600">Halaman untuk mengelola pedagang UMKM terdaftar.</p>
    
    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">← Kembali ke Dashboard</a>
    </div>
</div>
@endsection
