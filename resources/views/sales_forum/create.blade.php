<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800">Buat Post Penjualan Baru</h2>
    </x-slot>

    <div class="py-6 px-6">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-xl p-8">
            <form action="{{ route('sales_forum.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                        Judul Post <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        value="{{ old('title') }}"
                        placeholder="Contoh: Jasa Design Grafis Profesional"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                        required
                    >
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="category" 
                        name="category"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                        required
                    >
                        <option value="">-- Pilih Kategori --</option>
                        @foreach (['umum' => 'Umum', 'produk' => 'Produk', 'layanan' => 'Layanan', 'lowongan' => 'Lowongan Kerja'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description"
                        rows="6"
                        placeholder="Jelaskan detail produk/layanan Anda dengan lengkap..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                        required
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga (Opsional)
                    </label>
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Rp</span>
                        <input 
                            type="number" 
                            id="price" 
                            name="price" 
                            value="{{ old('price') }}"
                            placeholder="0"
                            class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                            min="0"
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika harga negosiasi</p>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Produk/Layanan (Opsional)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-green-500 hover:bg-green-50 transition" id="imageDropZone">
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                            class="hidden"
                        >
                        <div id="imagePlaceholder">
                            <p class="text-gray-600 text-lg">📷 Klik untuk upload atau drag & drop</p>
                            <p class="text-gray-500 text-sm mt-2">JPG, PNG, GIF (Max 2MB)</p>
                        </div>
                        <img id="imagePreview" class="hidden max-h-40 mx-auto mt-2">
                    </div>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Buttons -->
                <div class="flex gap-4 pt-6">
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 rounded-lg transition shadow-lg"
                    >
                        Posting Sekarang
                    </button>
                    <a 
                        href="{{ route('sales_forum.index') }}" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 rounded-lg transition text-center"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const imageDropZone = document.getElementById('imageDropZone');
        const imageInput = document.getElementById('image');
        const imagePlaceholder = document.getElementById('imagePlaceholder');
        const imagePreview = document.getElementById('imagePreview');

        imageDropZone.addEventListener('click', () => imageInput.click());

        imageDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageDropZone.classList.add('border-green-500', 'bg-green-50');
        });

        imageDropZone.addEventListener('dragleave', () => {
            imageDropZone.classList.remove('border-green-500', 'bg-green-50');
        });

        imageDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            imageDropZone.classList.remove('border-green-500', 'bg-green-50');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                displayImagePreview();
            }
        });

        imageInput.addEventListener('change', displayImagePreview);

        function displayImagePreview() {
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePlaceholder.classList.add('hidden');
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
