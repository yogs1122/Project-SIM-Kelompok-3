@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-4">Daftar Permintaan Upgrade UMKM</h2>

        @if(session('success'))
            <div class="mb-4 text-green-700">{{ session('success') }}</div>
        @endif

        <table class="w-full border-collapse">
            <thead>
                <tr class="text-left">
                    <th class="p-2">ID</th>
                    <th class="p-2">User</th>
                    <th class="p-2">Nama Usaha</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Dibuat</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $app)
                <tr class="border-t">
                    <td class="p-2">{{ $app->id }}</td>
                    <td class="p-2">{{ $app->user->name }} ({{ $app->user->email }})</td>
                    <td class="p-2">{{ $app->business_name }}</td>
                    <td class="p-2">{{ ucfirst($app->status) }}</td>
                    <td class="p-2">{{ $app->created_at->format('Y-m-d') }}</td>
                    <td class="p-2">
                        <a href="{{ route('admin.umkm_applications.show', $app) }}" class="text-indigo-600">Lihat</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection
