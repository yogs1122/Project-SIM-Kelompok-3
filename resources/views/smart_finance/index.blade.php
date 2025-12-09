<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Smart Finance Dashboard
        </h2>
    </x-slot>

    <div class="py-6 px-6">

        {{-- Ringkasan Keuangan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="p-5 bg-white shadow rounded-lg">
                <h3 class="text-gray-500 font-medium">Total Pemasukan</h3>
                <p class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($income, 0, ',', '.') }}</p>
            </div>

            <div class="p-5 bg-white shadow rounded-lg">
                <h3 class="text-gray-500 font-medium">Total Pengeluaran</h3>
                <p class="text-2xl font-bold text-red-600 mt-2">Rp {{ number_format($expense, 0, ',', '.') }}</p>
            </div>

            <div class="p-5 bg-white shadow rounded-lg">
                <h3 class="text-gray-500 font-medium">Saldo Bersih</h3>
                <p class="text-2xl font-bold text-indigo-600 mt-2">Rp {{ number_format($income - $expense, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Grafik / placeholder dulu --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
    <h3 class="font-semibold text-gray-700 mb-4">Grafik Keuangan Bulanan</h3>
    <canvas id="financeChart" height="120"></canvas>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="md:col-span-2 bg-white shadow rounded-lg p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Grafik Volatilitas Pengeluaran (3-bulan rolling)</h3>
        <canvas id="volatilityChart" height="120"></canvas>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Analisis Volatilitas</h3>
        @php
            $curVol = 0;
            $prevVol = 0;
            if (isset($monthlyStd) && method_exists($monthlyStd, 'isNotEmpty') && $monthlyStd->isNotEmpty()) {
                $curVol = round($monthlyStd->last());
                $prevVol = round($monthlyStd->get(max(0, $monthlyStd->count() - 2), 0));
            }
            $trend = $curVol > $prevVol ? 'Naik' : ($curVol < $prevVol ? 'Turun' : 'Stabil');
        @endphp

        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-600">
                    <div class="font-semibold text-gray-700 mb-2">Status Fluktuasi:</div>
                    <span class="inline-block bg-yellow-50 text-yellow-700 px-3 py-1 rounded text-base font-medium">{{ $trend }}</span>
                    @if ($trend === 'Naik')
                        <p class="text-xs text-gray-500 mt-2">📈 Pengeluaran semakin tidak stabil dari bulan lalu</p>
                    @elseif ($trend === 'Turun')
                        <p class="text-xs text-gray-500 mt-2">📉 Pengeluaran semakin stabil dibanding bulan lalu</p>
                    @else
                        <p class="text-xs text-gray-500 mt-2">➡️ Pengeluaran stabil seperti bulan lalu</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3 mt-4">
            <div class="p-3 bg-gray-50 rounded">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 bg-white/70 px-2 py-1 rounded">Coefficient of Variation</span>
                    <span class="text-xs text-gray-400">(CV)</span>
                </div>
                <div class="mt-2">
                    <span class="inline-block bg-yellow-50 text-yellow-700 px-3 py-2 rounded text-lg font-semibold">{{ isset($cv) ? round($cv,2) : '0.00' }}</span>
                </div>
            </div>

            <div class="p-3 bg-gray-50 rounded">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 bg-white/70 px-2 py-1 rounded">Fluktuasi Pengeluaran</span>
                </div>
                <div class="mt-2">
                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-2 rounded text-lg font-semibold">Rp {{ number_format($monthlyExpenseStd ?? 0,0,',','.') }}</span>
                </div>
            </div>

            <div class="p-3 bg-gray-50 rounded">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 bg-white/70 px-2 py-1 rounded">Rata-rata Transaksi</span>
                </div>
                <div class="mt-2">
                    <span class="inline-block bg-indigo-50 text-indigo-700 px-3 py-2 rounded text-lg font-semibold">Rp {{ number_format($avgTx ?? 0,0,',','.') }}</span>
                </div>
            </div>
        </div>

        <p class="text-sm text-gray-600 mt-4">Volatilitas dihitung sebagai standar deviasi pengeluaran pada jendela 3 bulan. Angka yang tinggi menandakan fluktuasi pengeluaran yang besar; pertimbangkan menyiapkan dana darurat atau anggaran lebih ketat.</p>
    </div>
</div>


        {{-- Transaksi Terbaru --}}
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-700 mb-4">Transaksi Terbaru</h3>

            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-left">Tipe</th>
                        <th class="p-2 text-left">Jumlah</th>
                        <th class="p-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestTransactions as $trx)
                        <tr class="border-b">
                            <td class="p-2">{{ $trx->created_at->format('d M Y') }}</td>
                            <td class="p-2 capitalize">{{ $trx->type }}</td>
                            <td class="p-2">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-2">{{ $trx->status }}</td>
                        </tr>
                    @endforeach

                    @if ($latestTransactions->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center py-3 text-gray-500">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Tips Keuangan --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Tips Smart Finance</h3>

           {{-- Tips Keuangan Dinamis --}}
@if (!empty($tips))
<div class="bg-blue-50 p-4 rounded-lg shadow mb-6">
    <h3 class="font-semibold text-blue-800 mb-2">Smart Finance Insights 💡</h3>
    <ul class="text-sm space-y-1 text-blue-600">
        @foreach ($tips as $tip)
            <li>• {{ $tip }}</li>
        @endforeach
    </ul>
</div>
@endif


        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('financeChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Pemasukan',
                    data: {!! json_encode($chartIncome) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.6)', // Tailwind Green
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($chartExpense) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.6)', // Tailwind Red
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // ===== Volatility (3-month rolling stddev) =====
    const volCtx = document.getElementById('volatilityChart').getContext('2d');
    new Chart(volCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [
                {
                    label: 'Volatilitas (Std Dev)',
                    data: {!! json_encode($monthlyStd ?? []) !!},
                    borderColor: 'rgba(249,115,22,0.9)',
                    backgroundColor: 'rgba(249,115,22,0.15)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

</script>



</x-app-layout>
