<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Template Rekomendasi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('admin.smartfinance.templates.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Judul</label>
                        <input name="title" class="w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Kategori</label>
                        <input name="category" class="w-full border rounded px-2 py-1" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Isi Template</label>
                        <textarea name="body" rows="6" class="w-full border rounded px-2 py-1" required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-3 py-2 bg-gray-200 rounded mr-2">Batal</button>
                        <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
