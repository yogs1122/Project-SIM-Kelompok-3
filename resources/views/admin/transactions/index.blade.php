<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Analytics Transaksi & User Clustering
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Quick Report Links -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('admin.transactions.topups') }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md">Top-up Report</a>
                <a href="{{ route('admin.transactions.transfers') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-md">Transfer Report</a>
                <a href="{{ route('admin.transactions.weekly') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md">Weekly History</a>
            </div>
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Total Pengguna</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $userActivity->total() }}</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Total Transaksi</h3>
                    <p class="text-3xl font-bold text-green-600">
                        {{ $recentTransactions->count() + ($userActivity->sum('transaction_count') - $recentTransactions->count()) }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Total Nilai Transaksi</h3>
                    <p class="text-3xl font-bold text-purple-600">
                        Rp {{ number_format($userActivity->sum('total_spent'), 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-2">Avg Per User</h3>
                    <p class="text-3xl font-bold text-orange-600">
                        Rp {{ number_format($userActivity->count() > 0 ? $userActivity->sum('total_spent') / $userActivity->count() : 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Transaction Type Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Transaksi Berdasarkan Tipe</h3>
                    <div class="space-y-3">
                        @forelse($transactionsByType as $trans)
                            <div class="flex items-center justify-between py-2 border-b">
                                <div>
                                    <p class="font-semibold text-gray-700 capitalize">{{ $trans->type }}</p>
                                    <p class="text-sm text-gray-500">{{ $trans->total }} transaksi</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">Rp {{ number_format($trans->total_amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">Rata-rata: Rp {{ number_format($trans->total_amount / $trans->total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Tidak ada transaksi</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">✓ Transaksi Berdasarkan Status</h3>
                    <div class="space-y-3">
                        @forelse($transactionsByStatus as $trans)
                            <div class="flex items-center justify-between py-2 border-b">
                                <div>
                                    <p class="font-semibold text-gray-700 capitalize">
                                        @if($trans->status === 'pending')
                                            🟡 Menunggu
                                        @elseif($trans->status === 'completed')
                                            ✓ Selesai
                                        @elseif($trans->status === 'failed')
                                            ✕ Gagal
                                        @else
                                            {{ $trans->status }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $trans->total }} transaksi</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">Rp {{ number_format($trans->total_amount ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Tidak ada transaksi</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- High Value Users (Clustering) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">💎 User Dengan Nilai Transaksi Tertinggi (Top 10)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">#</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Nama User</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Email</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-700">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Total Nilai</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Rata-rata Transaksi</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($highValueUsers as $index => $user)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-center font-semibold text-blue-600">{{ $user->transaction_count }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        Rp {{ number_format($user->total_spent, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-600">
                                        Rp {{ number_format($user->total_spent / $user->transaction_count, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.transactions.user-detail', $user) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                            📋 Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada user dengan transaksi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- All User Activity (Clustering) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">👥 Clustering User Berdasarkan Aktivitas Transaksi</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Nama</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Email</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-700">Jumlah Transaksi</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Total Nilai</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Terdaftar</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Klasifikasi</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userActivity as $user)
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-center font-semibold text-blue-600">{{ $user->transaction_count }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        Rp {{ number_format($user->total_spent ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if($user->transaction_count == 0)
                                            <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                🔵 No Activity
                                            </span>
                                        @elseif($user->transaction_count < 3)
                                            <span class="bg-yellow-200 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                🟡 Low Activity
                                            </span>
                                        @elseif($user->transaction_count < 10)
                                            <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                🔵 Medium Activity
                                            </span>
                                        @else
                                            <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                🟢 High Activity
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.transactions.user-detail', $user) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                                            📋 Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada user ditemukan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $userActivity->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
