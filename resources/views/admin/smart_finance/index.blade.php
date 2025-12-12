<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🧠 Admin Smart Finance</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg mb-4">Ringkasan Keuangan Pengguna</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-right">Pemasukan</th>
                                <th class="px-4 py-3 text-right">Pengeluaran</th>
                                <th class="px-4 py-3 text-right">Saldo Bersih</th>
                                <th class="px-4 py-3 text-center">Rekomendasi</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $u->name }}</td>
                                    <td class="px-4 py-3">{{ $u->email }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($u->income,0,',','.') }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($u->expense,0,',','.') }}</td>
                                    <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($u->net,0,',','.') }}</td>
                                    <td class="px-4 py-3 text-center">{{ $u->recommendations }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.smartfinance.show', $u->id) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">Lihat</a>
                                        <a href="{{ route('admin.smartfinance.templates.index') }}" class="px-3 py-1 ml-2 bg-gray-500 text-white rounded">Templates</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada pengguna</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
