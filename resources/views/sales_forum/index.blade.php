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

        <!-- Filter Lanjutan (Expandable) -->
        <div class="mb-6">
            <button id="toggle-filter" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center justify-between">
                <span>⚙️ Filter Lanjutan</span>
                <span id="toggle-icon">▼</span>
            </button>

            <!-- Filter Panel (Hidden by default) -->
            <div id="filter-panel" class="hidden bg-white shadow rounded-lg p-6 mt-4 border-t-4 border-blue-500">
                <form method="GET" action="{{ route('sales_forum.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Preserve search and category -->
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="category" value="{{ request('category', 'all') }}">

                    <!-- Subcategory -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">🏷️ Subkategori</label>
                        <select id="filter_subcategory" name="subcategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Subkategori</option>
                        </select>
                    </div>

                    <!-- Price Min -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">💰 Harga Minimal</label>
                        <input type="number" name="price_min" placeholder="Rp 0" value="{{ request('price_min') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Price Max -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">💰 Harga Maksimal</label>
                        <input type="number" name="price_max" placeholder="Rp 999999999" value="{{ request('price_max') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">↕️ Urutkan</label>
                        <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Default</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Rendah → Tinggi</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tinggi → Rendah</option>
                        </select>
                    </div>

                    <!-- Has Image (full width on last row) -->
                    <div class="md:col-span-4 flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer p-2 bg-blue-50 rounded-lg flex-1">
                            <input type="checkbox" name="has_image" value="1" {{ request('has_image') ? 'checked' : '' }} class="w-5 h-5">
                            <span class="text-sm font-medium text-gray-700">📷 Hanya tampilkan post dengan gambar</span>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="md:col-span-4 flex gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold text-sm transition">
                            ✓ Terapkan Filter
                        </button>
                        <a href="{{ route('sales_forum.index', ['search' => request('search'), 'category' => request('category', 'all')]) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-semibold text-sm transition text-center">
                            ↻ Reset
                        </a>
                    </div>
                </form>
            </div>
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

    <script>
        const subcategories = {
            produk: [
                { value: 'elektronik', label: 'Elektronik' },
                { value: 'pakaian', label: 'Pakaian' },
                { value: 'makanan_minuman', label: 'Makanan & Minuman' },
                { value: 'peralatan_rumah', label: 'Peralatan Rumah' },
                { value: 'kecantikan', label: 'Kecantikan' }
            ],
            layanan: [
                { value: 'kesehatan', label: 'Kesehatan' },
                { value: 'kebersihan', label: 'Kebersihan' },
                { value: 'pengiriman', label: 'Pengiriman' },
                { value: 'desain_kreatif', label: 'Desain & Kreatif' },
                { value: 'konsultasi', label: 'Konsultasi' }
            ],
            umum: [
                { value: 'tawaran', label: 'Tawaran' },
                { value: 'komunitas', label: 'Komunitas' },
                { value: 'lainnya', label: 'Lainnya' }
            ],
            lowongan: [
                { value: 'penuh_waktu', label: 'Penuh Waktu' },
                { value: 'paruh_waktu', label: 'Paruh Waktu' },
                { value: 'magang', label: 'Magang' },
                { value: 'freelance', label: 'Freelance' }
            ]
        };

        document.addEventListener('DOMContentLoaded', () => {
            // Toggle filter panel
            const toggleBtn = document.getElementById('toggle-filter');
            const filterPanel = document.getElementById('filter-panel');
            const toggleIcon = document.getElementById('toggle-icon');

            if (toggleBtn && filterPanel) {
                toggleBtn.addEventListener('click', () => {
                    filterPanel.classList.toggle('hidden');
                    toggleIcon.textContent = filterPanel.classList.contains('hidden') ? '▼' : '▲';
                });

                // Auto-expand if filter is applied
                const isFiltered = '{{ request('subcategory') || request('price_min') || request('price_max') || request('sort') || request('has_image') }}';
                if (isFiltered) {
                    filterPanel.classList.remove('hidden');
                    toggleIcon.textContent = '▲';
                }
            }

            // Populate subcategories
            const filterSub = document.getElementById('filter_subcategory');
            if (filterSub) {
                const cat = '{{ request('category', 'all') }}';
                filterSub.innerHTML = '<option value="">Semua Subkategori</option>';
                if (cat && cat !== 'all' && subcategories[cat]) {
                    subcategories[cat].forEach(opt => {
                        const el = document.createElement('option');
                        el.value = opt.value;
                        el.textContent = opt.label;
                        if (opt.value === '{{ request('subcategory', '') }}') el.selected = true;
                        filterSub.appendChild(el);
                    });
                }

                // Update subcategories when category changes (from main form)
                const categorySelect = document.querySelector('select[name="category"]');
                if (categorySelect) {
                    categorySelect.addEventListener('change', (e) => {
                        filterSub.innerHTML = '<option value="">Semua Subkategori</option>';
                        const newCat = e.target.value;
                        if (newCat && newCat !== 'all' && subcategories[newCat]) {
                            subcategories[newCat].forEach(opt => {
                                const el = document.createElement('option');
                                el.value = opt.value;
                                el.textContent = opt.label;
                                filterSub.appendChild(el);
                            });
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
