<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-semibold mb-2">Status Permintaan Upgrade UMKM</h3>
                    @if($umkmApplication)
                        <p><strong>Status:</strong> {{ ucfirst($umkmApplication->status) }}</p>
                        @if($umkmApplication->status == 'pending')
                            <p class="text-sm text-gray-600">Permintaan Anda sedang menunggu verifikasi admin.</p>
                        @elseif($umkmApplication->status == 'approved')
                            <p class="text-sm text-green-600">Permintaan disetujui — Anda sekarang memiliki akses UMKM.</p>
                        @else
                            <p class="text-sm text-red-600">Permintaan ditolak. Catatan admin: {{ $umkmApplication->admin_note }}</p>
                        @endif
                    @else
                        <p class="text-sm text-gray-600">Anda belum mengajukan permintaan upgrade.</p>
                        <a href="{{ route('umkm.upgrade') }}" class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded">Ajukan Upgrade UMKM</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
