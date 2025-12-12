<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📚 Template Rekomendasi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.smartfinance.templates.create') }}" class="px-3 py-2 bg-green-600 text-white rounded">Buat Template</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left">Title</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Updated</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $t)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $t->title }}</td>
                                <td class="px-4 py-3">{{ $t->category }}</td>
                                <td class="px-4 py-3">{{ $t->updated_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.smartfinance.templates.edit', $t->id) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
                                    <form action="{{ route('admin.smartfinance.templates.destroy', $t->id) }}" method="POST" style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-2 py-1 bg-red-600 text-white rounded" onclick="return confirm('Hapus template?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada template</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $templates->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
