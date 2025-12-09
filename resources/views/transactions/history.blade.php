<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi') }}
        </h2>
    </x-slot>

    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: slideInUp 0.6s ease-out;
        }

        .transaction-row {
            transition: all 0.3s ease;
        }

        .transaction-row:hover {
            background-color: #f9fafb;
            transform: translateX(4px);
        }
    </style>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 animate-in">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden relative">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-8 text-white">
                    <h3 class="text-3xl font-bold">📊 Riwayat Transaksi Lengkap</h3>
                    <p class="text-blue-100 mt-2">Lihat semua transaksi Anda</p>
                </div>

                <!-- Content -->
                <div class="p-8 relative">
                    <!-- Watermark removed: cleaned up noisy background text -->
                    @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-300">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal & Waktu</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Referensi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                <tr class="transaction-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-900">{{ $transaction->created_at->format('d M Y') }}</span>
                                            <span class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold 
                                            @if($transaction->type === 'topup') bg-green-100 text-green-800
                                            @elseif($transaction->type === 'transfer') bg-blue-100 text-blue-800
                                            @elseif($transaction->type === 'withdraw') bg-orange-100 text-orange-800
                                            @elseif($transaction->type === 'payment') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            <span class="mr-2">
                                                @if($transaction->type === 'topup') 💰
                                                @elseif($transaction->type === 'transfer') 🔄
                                                @elseif($transaction->type === 'withdraw') 💳
                                                @elseif($transaction->type === 'payment') 🧾
                                                @endif
                                            </span>
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <div class="max-w-xs truncate" title="{{ $transaction->description }}">
                                            {{ $transaction->description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold @if($transaction->type === 'topup') text-green-600 @else text-red-600 @endif">
                                            @if($transaction->type === 'topup')
                                                +Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                            @else
                                                -Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold 
                                            @if($transaction->status === 'completed') bg-green-100 text-green-800
                                            @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            @if($transaction->status === 'completed')
                                                ✓ Berhasil
                                            @elseif($transaction->status === 'pending')
                                                ⏳ Pending
                                            @else
                                                ✗ Gagal
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-xs bg-gray-100 px-3 py-2 rounded font-mono text-gray-700">
                                            {{ $transaction->reference_number }}
                                        </code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        {{ $transactions->links() }}
                    </div>
                    @else
                    <div class="text-center py-16">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-gray-500 text-xl font-semibold">Belum ada riwayat transaksi</p>
                        <p class="text-gray-400 mt-2">Mulai dengan membuat transaksi pertama Anda</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition hover:scale-105">
                            ← Kembali ke Dashboard
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
