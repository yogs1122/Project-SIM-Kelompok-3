@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Histori Transaksi Dompet</h1>

    <div class="mb-4">
        <a href="{{ route('umkm.wallet.show') }}" class="px-3 py-2 bg-gray-100 rounded">Kembali ke Dompet</a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Waktu</th>
                    <th class="p-3 text-left">Tipe</th>
                    <th class="p-3 text-right">Jumlah (Rp)</th>
                    <th class="p-3 text-left">Sumber / Target</th>
                    <th class="p-3 text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr class="border-t">
                        <td class="p-3 text-sm">{{ $tx->created_at->format('d M Y H:i') }}</td>
                        <td class="p-3 text-sm">{{ $tx->type }}</td>
                        <td class="p-3 text-sm text-right">{{ number_format($tx->amount,2,',','.') }}</td>
                        <td class="p-3 text-sm">{{ $tx->source }} → {{ $tx->target }}</td>
                        <td class="p-3 text-sm">{{ optional($tx->meta)['note'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4" colspan="5">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
