@props(['title' => '', 'icon' => '', 'color' => 'blue', 'currentBalance' => 0])

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

    .btn-submit {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
    }

    .btn-submit::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-submit:hover::after {
        left: 100%;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-header.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .card-header.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .card-header.orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }

    .card-header.purple {
        background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%);
    }
</style>

<div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-md mx-auto sm:px-6 lg:px-8 animate-in">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="card-header {{ $color }} p-8 text-white">
                <div class="text-4xl mb-2">{{ $icon }}</div>
                <h3 class="text-2xl font-bold">{{ $title }}</h3>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Balance Card -->
                <div class="mb-8 bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border-2 border-blue-200">
                    <p class="text-sm text-blue-600 font-semibold uppercase tracking-wide mb-2">💳 Saldo Anda</p>
                    <p class="text-4xl font-bold text-blue-700">Rp {{ number_format($currentBalance, 0, ',', '.') }}</p>
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

                <!-- Form Slot -->
                {{ $slot }}

                <!-- Back Link -->
                <div class="mt-8 text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition hover:scale-105">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
