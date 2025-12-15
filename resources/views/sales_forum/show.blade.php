<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Detail Post</h2>
            @if ($salesForum->user_id === Auth::id())
                <div class="flex gap-2">
                    <a href="{{ route('sales_forum.edit', $salesForum) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                        ✏️ Edit
                    </a>
                    <form action="{{ route('sales_forum.destroy', $salesForum) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus post ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition">
                            🗑️ Hapus
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6 px-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded">
                {{ $errors->first() }}
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Image -->
                <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-6">
                    @if ($salesForum->image)
                        <img src="{{ asset('storage/' . $salesForum->image) }}" alt="{{ $salesForum->title }}" class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                            <span class="text-gray-400 text-6xl">📦</span>
                        </div>
                    @endif
                </div>

                <!-- Title & Details -->
                <div class="bg-white shadow-lg rounded-xl p-8 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $salesForum->title }}</h1>
                            <div class="flex gap-2 mb-4">
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">{{ ucfirst($salesForum->category) }}</span>
                                @if ($salesForum->status === 'sold')
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">TERJUAL</span>
                                @else
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">AKTIF</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Price -->
                    @if ($salesForum->price)
                        <div class="text-4xl font-bold text-green-600 mb-6 bg-green-50 px-6 py-4 rounded-lg inline-block">
                            Rp {{ number_format($salesForum->price, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="text-2xl font-semibold text-gray-600 mb-6 bg-yellow-50 px-6 py-4 rounded-lg inline-block">
                            💰 Harga Negosiasi
                        </div>
                    @endif

                    <!-- Seller Info -->
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded mb-6">
                        <p class="text-sm text-gray-600 mb-2">Dari:</p>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($salesForum->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $salesForum->user->name }}</p>
                                @if ($salesForum->user->phone)
                                    <p class="text-sm text-gray-600">📱 {{ $salesForum->user->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Deskripsi:</h3>
                    <div class="bg-gray-50 p-6 rounded-lg mb-6 text-gray-700 whitespace-pre-wrap leading-relaxed">
                        {{ $salesForum->description }}
                    </div>

                    <!-- Metadata -->
                    <div class="border-t pt-4 flex justify-between text-sm text-gray-500">
                        <span>📅 {{ $salesForum->created_at->locale('id')->diffForHumans() }}</span>
                        <span>👁 {{ $salesForum->views }} Views</span>
                    </div>
                </div>

                <!-- Mark as Sold Button -->
                @if ($salesForum->user_id === Auth::id() && $salesForum->status === 'active')
                    <form action="{{ route('sales_forum.mark_sold', $salesForum) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-lg transition shadow-lg">
                            ✅ Tandai sebagai Terjual
                        </button>
                    </form>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Contact Card -->
                @if ($salesForum->user_id !== Auth::id())
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg rounded-xl p-6 sticky top-20 text-white">
                        <h3 class="text-xl font-bold mb-4">Hubungi Penjual</h3>
                        
                        @if ($salesForum->user->phone)
                            <div class="mb-4">
                                <p class="text-sm opacity-90 mb-2">Nomor WhatsApp:</p>
                                <a href="https://wa.me/{{ str_replace(['(', ')', ' ', '-'], '', $salesForum->user->phone) }}" target="_blank" class="block bg-white text-green-600 px-4 py-3 rounded-lg font-bold text-center hover:bg-gray-100 transition">
                                    💬 Chat WhatsApp
                                </a>
                            </div>
                        @endif

                        <div class="bg-white bg-opacity-20 p-4 rounded-lg">
                            <p class="text-sm font-semibold mb-2">💡 Tips:</p>
                            <p class="text-xs opacity-95">Konfirmasi keaslian produk sebelum melakukan transaksi. Gunakan fitur wallet untuk pembayaran yang lebih aman.</p>
                        </div>
                        @if($salesForum->price)
                            <div class="mt-4">
                                <form action="{{ route('sales_forum.purchase', $salesForum) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full mt-3 bg-white text-green-600 font-bold py-3 rounded-lg transition shadow-lg">🛒 Beli Sekarang (pakai Wallet)</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-6 sticky top-20">
                        <h3 class="text-lg font-bold text-blue-900 mb-4">📊 Statistik Post Anda</h3>
                        <div class="space-y-3">
                            <div class="bg-white p-3 rounded">
                                <p class="text-xs text-gray-600">Total Views</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $salesForum->views }}</p>
                            </div>
                            <div class="bg-white p-3 rounded">
                                <p class="text-xs text-gray-600">Status</p>
                                <p class="text-lg font-bold text-{{ $salesForum->status === 'sold' ? 'red' : 'green' }}-600">
                                    {{ $salesForum->status === 'sold' ? '❌ Terjual' : '✅ Aktif' }}
                                </p>
                            </div>
                            <div class="bg-white p-3 rounded">
                                <p class="text-xs text-gray-600">Dibuat</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $salesForum->created_at->locale('id')->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('sales_forum.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-2">
                ← Kembali ke Forum
            </a>
        </div>
    </div>
</x-app-layout>
