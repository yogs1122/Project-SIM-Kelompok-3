<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📤 Laporan Transfer</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-sm">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-2 py-1" />
                    <label class="text-sm">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-2 py-1" />
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-2 bg-gray-200 rounded">Filter</button>
                    <button type="submit" name="export" value="csv" class="px-3 py-2 bg-blue-600 text-white rounded">Export CSV</button>
                </div>
            </form>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg mb-4">Transfer per User</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-right">Terkirim (Jumlah)</th>
                                <th class="px-4 py-3 text-right">Terkirim (Total)</th>
                                <th class="px-4 py-3 text-right">Diterima (Jumlah)</th>
                                <th class="px-4 py-3 text-right">Diterima (Total)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $u->name }}</td>
                                    <td class="px-4 py-3">{{ $u->email }}</td>
                                    <td class="px-4 py-3 text-right">{{ $u->sent_count }}</td>
                                    <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($u->sent_total,0,',','.') }}</td>
                                    <td class="px-4 py-3 text-right">{{ $u->received_count }}</td>
                                    <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($u->received_total,0,',','.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada data transfer</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
