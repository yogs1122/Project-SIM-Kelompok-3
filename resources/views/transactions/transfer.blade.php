<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transfer Dana') }}
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

        .btn-transfer {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-transfer:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3);
        }

        .card-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
    </style>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8 animate-in">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="card-header p-6 text-white">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/logos/transfer.svg') }}" alt="Transfer" class="w-14 h-14" />
                        <div>
                            <h3 class="text-2xl font-bold">Transfer Dana</h3>
                            <p class="text-blue-100 mt-1">Kirim dana ke user lain dengan aman</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="mb-8 bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border-2 border-blue-200">
                        <p class="text-sm text-blue-600 font-semibold uppercase tracking-wide mb-2">💳 Saldo Anda</p>
                        <p class="text-4xl font-bold text-blue-700">Rp {{ number_format($wallet->balance ?? 0, 0, ',', '.') }}</p>
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

                    <form action="{{ route('transactions.transfer.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Penerima</label>
                            <select 
                                name="recipient_id" 
                                class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 font-semibold"
                                required
                            >
                                <option value="">-- Pilih Penerima --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('recipient_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Jumlah Transfer</label>
                            <div class="relative">
                                <span class="absolute left-4 top-4 text-gray-400 font-semibold">Rp</span>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    value="{{ old('amount') }}"
                                    placeholder="Minimal Rp 1.000" 
                                    step="1000"
                                    min="1000"
                                    max="10000000"
                                    class="input-focus w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-lg font-semibold"
                                    required
                                >
                            </div>
                            @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Catatan (Opsional)</label>
                            <textarea 
                                name="description" 
                                placeholder="Contoh: pembayaran utang, hadiah, dll"
                                rows="3"
                                class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            ></textarea>
                            @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button 
                            type="submit" 
                            class="btn-transfer w-full text-white font-bold py-4 rounded-lg text-lg"
                        >
                            ✓ Kirim Transfer
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
