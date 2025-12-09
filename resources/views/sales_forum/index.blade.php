<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Forum Penjualan & Lowongan UMKM</h2>
            <a href="{{ route('sales_forum.create') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-2 rounded-lg font-semibold shadow-lg transition">
                ✚ Buat Post Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-6">
        <!-- Search & Filter Bar -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('sales_forum.index') }}" class="flex flex-col md:flex-row gap-4">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Cari post... (judul atau deskripsi)" 
                    value="{{ request('search') }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="all">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Posts Grid -->
        @if ($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach ($posts as $post)
                    <a href="{{ route('sales_forum.show', $post) }}" class="group">
                        <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition duration-300 h-full flex flex-col">
                            <!-- Image Container -->
                            <div class="relative bg-gradient-to-br from-gray-200 to-gray-300 h-48 overflow-hidden">
                                @if ($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-gray-400 text-4xl">📦</span>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                @if ($post->status === 'sold')
                                    <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">TERJUAL</div>
                                @else
                                    <div class="absolute top-3 right-3 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">AKTIF</div>
                                @endif

                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3 bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">{{ ucfirst($post->category) }}</div>
                            </div>

                            <!-- Content Container -->
                            <div class="p-4 flex-1 flex flex-col">
                                <!-- Title -->
                                <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2 group-hover:text-green-600 transition">
                                    {{ $post->title }}
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    {{ $post->description }}
                                </p>

                                <!-- User Info -->
                                <div class="flex items-center gap-2 mb-3 text-xs text-gray-500">
                                    <span>👤 {{ $post->user->name ?? 'Anonymous' }}</span>
                                </div>

                                <!-- Footer with Price & Views -->
                                <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-200">
                                    @if ($post->price)
                                        <span class="text-lg font-bold text-green-600">Rp {{ number_format($post->price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-sm text-gray-500">Harga Negosiasi</span>
                                    @endif
                                    <span class="text-xs text-gray-400">👁 {{ $post->views }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-12 text-center">
                <p class="text-gray-600 text-lg mb-4">Belum ada post yang sesuai dengan pencarian Anda.</p>
                <a href="{{ route('sales_forum.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-semibold transition">
                    Buat Post Pertama Anda
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
