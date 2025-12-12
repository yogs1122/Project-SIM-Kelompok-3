<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📅 Riwayat Transaksi (7 Hari Terakhir)</h2>
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
                    <button type="submit" name="export" value="csv" class="px-3 py-2 bg-indigo-600 text-white rounded">Export CSV</button>
                </div>
            </form>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg mb-4">Ringkasan Per User</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-right">Jumlah Transaksi</th>
                                <th class="px-4 py-3 text-right">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grouped as $g)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $g['user_name'] }}</td>
                                    <td class="px-4 py-3">{{ $g['user_email'] }}</td>
                                    <td class="px-4 py-3 text-right">{{ $g['count'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($g['total'],0,',','.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Tidak ada transaksi dalam 7 hari terakhir</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h3 class="font-semibold text-lg mb-4">Detail Transaksi (urut waktu)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Tipe</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Referensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ $t->user_name }} ({{ $t->user_email }})</td>
                                    <td class="px-4 py-3">{{ ucfirst($t->type) }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($t->amount,0,',','.') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($t->status) }}</td>
                                    <td class="px-4 py-3">{{ $t->reference_number }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
