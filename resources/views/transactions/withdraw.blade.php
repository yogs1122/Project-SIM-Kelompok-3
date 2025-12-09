<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tarik Dana (Withdraw)') }}
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

        .btn-withdraw {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-withdraw:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(249, 115, 22, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }
    </style>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8 animate-in">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="card-header p-6 text-white">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/logos/withdraw.svg') }}" alt="Withdraw" class="w-14 h-14" />
                        <div>
                            <h3 class="text-2xl font-bold">Tarik Dana</h3>
                            <p class="text-orange-100 mt-1">Withdraw ke rekening bank Anda</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="mb-8 bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl border-2 border-orange-200">
                        <p class="text-sm text-orange-600 font-semibold uppercase tracking-wide mb-2">💳 Saldo Anda</p>
                        <p class="text-4xl font-bold text-orange-700">Rp {{ number_format($wallet->balance ?? 0, 0, ',', '.') }}</p>
                    </div>

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

                    <form action="{{ route('transactions.withdraw.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Jumlah Withdraw</label>
                            <div class="relative">
                                <span class="absolute left-4 top-4 text-gray-400 font-semibold">Rp</span>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    value="{{ old('amount') }}"
                                    placeholder="Minimal Rp 50.000" 
                                    step="1000"
                                    min="50000"
                                    max="10000000"
                                    class="input-focus w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 text-lg font-semibold"
                                    required
                                >
                            </div>
                            @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Nama Bank</label>
                            <input 
                                type="text" 
                                name="bank_name" 
                                value="{{ old('bank_name') }}"
                                placeholder="Contoh: BCA, Mandiri, BNI" 
                                class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 font-semibold"
                                required
                            >
                            @error('bank_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Nomor Rekening</label>
                            <input 
                                type="text" 
                                name="account_number" 
                                value="{{ old('account_number') }}"
                                placeholder="Masukkan nomor rekening" 
                                class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 font-semibold"
                                required
                            >
                            @error('account_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Atas Nama</label>
                            <input 
                                type="text" 
                                name="account_holder" 
                                value="{{ old('account_holder') }}"
                                placeholder="Nama pemilik rekening" 
                                class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-orange-500 font-semibold"
                                required
                            >
                            @error('account_holder')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 rounded">
                            <p class="font-bold text-sm">💡 Informasi</p>
                            <p class="text-xs mt-1">Withdraw akan diproses dalam 1-2 hari kerja</p>
                        </div>

                        <button 
                            type="submit" 
                            class="btn-withdraw w-full text-white font-bold py-4 rounded-lg text-lg"
                        >
                            ✓ Tarik Dana
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition hover:scale-105">
                            ← Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
