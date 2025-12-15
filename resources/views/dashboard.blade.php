<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
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

        @keyframes pulse-scale {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-in {
            animation: slideInUp 0.6s ease-out;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .btn-transaction {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-transaction::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-transaction:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-transaction:hover {
            transform: scale(1.08);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .icon-float {
            animation: float 3s ease-in-out infinite;
            display: inline-block;
        }

        .balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        .balance-card::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        .balance-content {
            position: relative;
            z-index: 1;
        }

        .transaction-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .transaction-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Role-specific verification banner --}}
            @if(isset($isSeller) && $isSeller)
                <div class="mb-6 p-4 rounded-lg bg-blue-50 border-l-4 border-blue-400">
                    <div class="font-semibold text-blue-800">Seller Dashboard (verified)</div>
                    <div class="text-sm text-blue-700">Anda melihat dashboard sebagai <strong>seller</strong>. Merchant wallet: <strong>Rp {{ number_format(optional($merchantWallet)->balance ?? 0, 0, ',', '.') }}</strong></div>
                </div>
            @else
                <div class="mb-6 p-4 rounded-lg bg-gray-50 border-l-4 border-gray-300">
                    <div class="font-semibold text-gray-800">User Dashboard</div>
                    <div class="text-sm text-gray-600">Anda melihat dashboard sebagai pengguna biasa.</div>
                </div>
            @endif
            <!-- Wallet Summary Card -->
            @if($wallet)
            <div class="mb-8 animate-in">
                <div class="balance-card rounded-2xl shadow-xl p-8 text-white relative">
                    <div class="balance-content">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <p class="text-white text-opacity-80 text-sm font-medium mb-2">💳 Saldo Wallet Anda</p>
                                <h1 class="text-5xl font-bold">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h1>
                            </div>
                            <img src="{{ asset('images/logos/ewallet-logo.svg') }}" alt="E-Wallet Logo" class="w-24 h-24 opacity-90">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white text-opacity-70 text-xs uppercase tracking-wider mb-1">Account Number</p>
                                <p class="text-xl font-semibold">{{ $wallet->account_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-white text-opacity-70 text-xs uppercase tracking-wider mb-1">User</p>
                                <p class="text-lg font-semibold">{{ Auth::user()->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Menu Transaksi dengan Animasi -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="mb-8 animate-in lg:col-span-2" style="animation-delay: 0.1s;">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="text-3xl mr-3">⚡</span>
                        Fitur Transaksi Cepat
                    </h3>
                    
                    <div class="transaction-grid">
                        @if(!Auth::user() || !Auth::user()->isAdmin())
                        <!-- Top Up Button -->
                        <a href="{{ route('transactions.topup') }}" class="btn-transaction card-hover group">
                            <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 rounded-xl p-6 text-white flex flex-col items-center justify-center text-center relative overflow-hidden hover:shadow-2xl transition-shadow">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative z-10">
                                    <img src="{{ asset('images/logos/topup.svg') }}" alt="Top Up" class="w-20 h-20 mx-auto mb-4 icon-float">
                                    <h4 class="text-2xl font-bold mb-1">Top Up Wallet</h4>
                                    <p class="text-green-100 text-sm mb-2">Tambah Saldo</p>
                                    <p class="text-green-50 text-xs leading-relaxed">Isi saldo dompet Anda dengan mudah dan aman</p>
                                </div>
                            </div>
                        </a>
                        @endif

                        <!-- Transfer Button -->
                        <a href="{{ route('transactions.transfer') }}" class="btn-transaction card-hover group">
                            <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl p-6 text-white flex flex-col items-center justify-center text-center relative overflow-hidden hover:shadow-2xl transition-shadow">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative z-10">
                                    <img src="{{ asset('images/logos/transfer.svg') }}" alt="Transfer" class="w-20 h-20 mx-auto mb-4 icon-float" style="animation-delay: 0.2s;">
                                    <h4 class="text-2xl font-bold mb-1">Transfer Dana</h4>
                                    <p class="text-blue-100 text-sm mb-2">Kirim Uang</p>
                                    <p class="text-blue-50 text-xs leading-relaxed">Kirim dana ke pengguna lain dengan cepat</p>
                                </div>
                            </div>
                        </a>

                        <!-- Withdraw Button -->
                        <a href="{{ route('transactions.withdraw') }}" class="btn-transaction card-hover group">
                            <div class="h-48 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl p-6 text-white flex flex-col items-center justify-center text-center relative overflow-hidden hover:shadow-2xl transition-shadow">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative z-10">
                                    <img src="{{ asset('images/logos/withdraw.svg') }}" alt="Withdraw" class="w-20 h-20 mx-auto mb-4 icon-float" style="animation-delay: 0.4s;">
                                    <h4 class="text-2xl font-bold mb-1">Tarik Dana</h4>
                                    <p class="text-orange-100 text-sm mb-2">Withdraw</p>
                                    <p class="text-orange-50 text-xs leading-relaxed">Tarik dana ke rekening bank Anda</p>
                                </div>
                            </div>
                        </a>

                        <!-- Payment Button -->
                        <a href="{{ route('transactions.payment') }}" class="btn-transaction card-hover group">
                            <div class="h-48 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl p-6 text-white flex flex-col items-center justify-center text-center relative overflow-hidden hover:shadow-2xl transition-shadow">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative z-10">
                                    <img src="{{ asset('images/logos/payment.svg') }}" alt="Payment" class="w-20 h-20 mx-auto mb-4 icon-float" style="animation-delay: 0.6s;">
                                    <h4 class="text-2xl font-bold mb-1">Pembayaran</h4>
                                    <p class="text-purple-100 text-sm mb-2">Bayar</p>
                                    <p class="text-purple-50 text-xs leading-relaxed">Bayar tagihan air, listrik, internet</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Section (Sidebar) -->
            <div class="bg-white rounded-2xl shadow-lg p-8 animate-in lg:col-span-1 sticky top-20" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <span class="text-3xl mr-3">📊</span>
                        Riwayat Transaksi Terbaru
                    </h3>
                    <a href="{{ route('transactions.history') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105">
                        Lihat Semua
                        <span class="ml-2">→</span>
                    </a>
                </div>
                
                @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Referensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
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
                                    <code class="text-xs bg-gray-100 px-3 py-1 rounded font-mono text-gray-700">{{ $transaction->reference_number }}</code>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📭</div>
                    <p class="text-gray-500 text-lg">Belum ada riwayat transaksi</p>
                    <p class="text-gray-400 text-sm mt-2">Mulai dengan membuat transaksi pertama Anda</p>
                </div>
                @endif
                </div>
            </div>
            </div>
    </div>
</x-app-layout>
