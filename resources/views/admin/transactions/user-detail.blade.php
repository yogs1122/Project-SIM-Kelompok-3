<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👤 Detail User: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.transactions.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- User Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Nama</h3>
                        <p class="text-lg font-bold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Email</h3>
                        <p class="text-lg font-bold text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">No. Telepon</h3>
                        <p class="text-lg font-bold text-gray-800">{{ $user->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-1">Role</h3>
                        @forelse($user->roles as $role)
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ ucfirst($role->name) }}
                            </span>
                        @empty
                            <p class="text-gray-600">-</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Total Transaksi</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $statistics['total_transactions'] }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Total Pengeluaran</h3>
                    <p class="text-2xl font-bold text-green-600">
                        Rp {{ number_format($statistics['total_spent'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Rata-rata Transaksi</h3>
                    <p class="text-2xl font-bold text-purple-600">
                        Rp {{ number_format($statistics['average_transaction'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Lama Berlangganan</h3>
                    <p class="text-3xl font-bold text-orange-600">{{ $statistics['subscription_days'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Hari</p>
                </div>
            </div>

            <!-- Membership Info -->
            <div class="bg-blue-50 border border-blue-200 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-blue-800 uppercase mb-2">📅 Informasi Keanggotaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <p class="text-blue-700">
                        <strong>Terdaftar Sejak:</strong> {{ $user->created_at->format('d M Y H:i') }}
                    </p>
                    <p class="text-blue-700">
                        <strong>Status Verifikasi Email:</strong> 
                        @if($user->email_verified_at)
                            ✓ Terverifikasi ({{ $user->email_verified_at->format('d M Y') }})
                        @else
                            ✕ Belum Terverifikasi
                        @endif
                    </p>
                </div>
            </div>

            <!-- User Transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">💳 Riwayat Transaksi</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Tipe</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Deskripsi</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userTransactions as $transaction)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-gray-600">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold capitalize">
                                            {{ $transaction->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $transaction->description ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($transaction->status === 'completed')
                                            <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                ✓ Selesai
                                            </span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                ⏳ Menunggu
                                            </span>
                                        @elseif($transaction->status === 'failed')
                                            <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                ✕ Gagal
                                            </span>
                                        @else
                                            <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold capitalize">
                                                {{ $transaction->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        User ini belum memiliki transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $userTransactions->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
