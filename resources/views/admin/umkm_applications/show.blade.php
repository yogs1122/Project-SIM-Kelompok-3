@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="bg-white shadow rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Permintaan Upgrade UMKM #{{ $application->id }}</h2>

        <div class="mb-4">
            <strong>User:</strong> {{ $application->user->name }} ({{ $application->user->email }})
        </div>
        <div class="mb-4">
            <strong>Nama Usaha:</strong> {{ $application->business_name }}
        </div>
        <div class="mb-4">
            <strong>Owner:</strong> {{ $application->owner_name ?? '-' }}
        </div>
        <div class="mb-4">
            <strong>Phone:</strong> {{ $application->phone ?? '-' }}
        </div>
        <div class="mb-4">
            <strong>Address:</strong> <div class="mt-1 p-2 bg-gray-50">{{ $application->address ?? '-' }}</div>
        </div>

        @if($application->document_path)
        <div class="mb-4">
            <strong>Document:</strong>
            <div class="mt-1">
                <a href="{{ asset('storage/' . $application->document_path) }}" target="_blank" class="text-indigo-600">Lihat dokumen</a>
            </div>
        </div>
        @endif

        <hr class="my-4">

        <form method="POST" action="{{ route('admin.umkm_applications.approve', $application) }}" class="mb-4">
            @csrf
            <div class="mb-2">
                <label class="block text-sm font-medium">Catatan (opsional)</label>
                <textarea name="admin_note" class="mt-1 block w-full border-gray-200 rounded p-2"></textarea>
            </div>
            <button class="px-4 py-2 bg-green-600 text-white rounded">Setujui</button>
        </form>

        <form method="POST" action="{{ route('admin.umkm_applications.reject', $application) }}">
            @csrf
            <div class="mb-2">
                <label class="block text-sm font-medium">Alasan penolakan (wajib)</label>
                <textarea name="admin_note" required class="mt-1 block w-full border-gray-200 rounded p-2"></textarea>
            </div>
            <button class="px-4 py-2 bg-red-600 text-white rounded">Tolak</button>
        </form>

    </div>
</div>
@endsection
