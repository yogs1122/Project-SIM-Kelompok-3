<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Top Up Wallet') }}
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

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .btn-topup {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-topup:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.3);
        }

        .btn-topup::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-topup:hover::after {
            left: 100%;
        }

        .quick-button {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .quick-button:hover {
            transform: scale(1.05);
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8 animate-in">
            <!-- Card Container -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header dengan Gradient -->
                <div class="card-header p-6 text-white">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/logos/topup.svg') }}" alt="Topup" class="w-14 h-14" />
                        <div>
                            <h3 class="text-2xl font-bold">Top Up Wallet</h3>
                            <p class="text-white text-opacity-90 mt-1">Tambahkan saldo ke dompet digital Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <!-- Saldo Saat Ini -->
                    <div class="mb-8 bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border-2 border-blue-200">
                        <p class="text-sm text-blue-600 font-semibold uppercase tracking-wide mb-2">💳 Saldo Saat Ini</p>
                        <p class="text-4xl font-bold text-blue-700">Rp {{ number_format($wallet->balance ?? 0, 0, ',', '.') }}</p>
                    </div>

                    <!-- Alert Messages -->
                    @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-800 rounded">
                        <p class="font-bold mb-2">⚠️ Terjadi Kesalahan</p>
                        <ul class="text-sm">
                            @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-800 rounded">
                        <p class="font-bold mb-1">✓ Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-800 rounded">
                        <p class="font-bold mb-1">✗ Error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                    @endif

                    <!-- Form Top Up -->
                    <form action="{{ route('transactions.topup.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Jumlah Top Up</label>
                            <div class="relative">
                                <span class="absolute left-4 top-4 text-gray-400 font-semibold">Rp</span>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    value="{{ old('amount') }}"
                                    placeholder="Minimal Rp 10.000" 
                                    step="1000"
                                    min="10000"
                                    max="10000000"
                                    class="input-focus w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-lg font-semibold"
                                    required
                                >
                            </div>
                            @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preset Buttons -->
                        <div>
                            <p class="text-sm font-bold text-gray-700 mb-3">📌 Pilihan Cepat:</p>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" onclick="document.querySelector('input[name=amount]').value = 50000; document.querySelector('input[name=amount]').focus(); this.parentElement.parentElement.querySelector('.quick-button').classList.remove('bg-blue-50', 'border-blue-400'); this.classList.add('bg-blue-50', 'border-blue-400');" class="quick-button px-4 py-3 bg-gray-50 text-gray-700 rounded-lg text-sm font-bold hover:bg-blue-50 border-2 border-gray-200">
                                    Rp 50.000
                                </button>
                                <button type="button" onclick="document.querySelector('input[name=amount]').value = 100000; document.querySelector('input[name=amount]').focus();" class="quick-button px-4 py-3 bg-gray-50 text-gray-700 rounded-lg text-sm font-bold hover:bg-blue-50 border-2 border-gray-200">
                                    Rp 100.000
                                </button>
                                <button type="button" onclick="document.querySelector('input[name=amount]').value = 500000; document.querySelector('input[name=amount]').focus();" class="quick-button px-4 py-3 bg-gray-50 text-gray-700 rounded-lg text-sm font-bold hover:bg-blue-50 border-2 border-gray-200">
                                    Rp 500.000
                                </button>
                                <button type="button" onclick="document.querySelector('input[name=amount]').value = 1000000; document.querySelector('input[name=amount]').focus();" class="quick-button px-4 py-3 bg-gray-50 text-gray-700 rounded-lg text-sm font-bold hover:bg-blue-50 border-2 border-gray-200">
                                    Rp 1.000.000
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="btn-topup w-full text-white font-bold py-4 rounded-lg text-lg"
                        >
                            ✓ Top Up Sekarang
                        </button>
                    </form>

                    <!-- Back Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition hover:scale-105">
                            ← Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
