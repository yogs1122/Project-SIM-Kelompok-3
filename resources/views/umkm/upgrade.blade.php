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

        <form method="POST" action="{{ route('umkm.upgrade.post') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Usaha</label>
                <input type="text" name="business_name" value="{{ old('business_name') }}" class="mt-1 block w-full border-gray-200 rounded p-2" required>
                @error('business_name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Nama Pemilik (opsional)</label>
                <input type="text" name="owner_name" value="{{ old('owner_name') }}" class="mt-1 block w-full border-gray-200 rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Nomor Telepon (opsional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-200 rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Alamat (opsional)</label>
                <textarea name="address" class="mt-1 block w-full border-gray-200 rounded p-2">{{ old('address') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Unggah Dokumen Verifikasi (KTP / SIUP) - opsional</label>
                <input type="file" name="document" class="mt-1 block w-full">
                @error('document') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Kirim Permintaan Upgrade</button>
                <a href="{{ route('profile.edit') }}" class="ml-4 text-sm px-3 py-2 bg-gray-200 text-gray-800 rounded">Batal</a>
            </div>
        </form>

        <hr class="my-6">
        <p class="text-sm text-gray-600">Catatan: Permintaan Anda akan dikirim ke tim admin untuk verifikasi. Setelah disetujui, akun Anda akan memperoleh kemampuan membuat posting penjualan.</p>

        <!-- Secondary prominent submit for visibility on all screens -->
        <div class="mt-4">
            <form method="POST" action="{{ route('umkm.upgrade.post') }}" enctype="multipart/form-data">
                @csrf
                <!-- Prominent full-width submit button -->
                <button type="submit" class="w-full px-4 py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700">Kirim Permintaan Upgrade</button>
            </form>
        </div>
    </div>
</div>
@endsection
