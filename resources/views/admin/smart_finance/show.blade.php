<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🧾 Smart Finance - {{ $user->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-lg">Ringkasan</h3>
                        <p class="text-sm text-gray-600">Pemasukan: Rp {{ number_format($income,0,',','.') }} | Pengeluaran: Rp {{ number_format($expense,0,',','.') }} | Saldo Bersih: Rp {{ number_format($net,0,',','.') }}</p>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.smartfinance.recommend', $user->id) }}">
                            @csrf
                            <div class="flex items-center gap-2">
                                <select id="template_select" name="template_id" class="border px-2 py-1 rounded">
                                    <option value="">— Pilih Template (opsional) —</option>
                                    @foreach($templates ?? collect() as $t)
                                        <option value="{{ $t->id }}" data-body="{{ e($t->body) }}">{{ $t->title }} @if($t->category) ({{ $t->category }})@endif</option>
                                    @endforeach
                                </select>

                                <textarea id="message_field" name="message" placeholder="Tulis rekomendasi..." class="border px-2 py-1 rounded w-96 h-16" required></textarea>

                                <button type="submit" class="px-3 py-2 bg-green-600 text-white rounded">Kirim</button>
                            </div>
                        </form>

                        <script>
                            (function(){
                                const sel = document.getElementById('template_select');
                                const msg = document.getElementById('message_field');
                                sel?.addEventListener('change', function(){
                                    const opt = sel.options[sel.selectedIndex];
                                    const body = opt?.dataset?.body ?? '';
                                    if(body) msg.value = body;
                                });
                            })();
                        </script>
                    </div>
                </div>

                <h4 class="font-semibold mb-2">Rekomendasi Sebelumnya</h4>
                <div class="space-y-2 mb-6">
                    @forelse($recommendations as $r)
                        <div class="p-3 bg-gray-50 rounded">
                            <div class="text-sm text-gray-600">Dari: {{ $r->admin->name }} — {{ $r->created_at->format('d M Y H:i') }}</div>
                            <div class="mt-1">{{ $r->message }}</div>
                        </div>
                    @empty
                        <div class="text-gray-500">Belum ada rekomendasi</div>
                    @endforelse
                </div>

                <h4 class="font-semibold mb-2">Transaksi Terbaru</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Waktu</th>
                                <th class="px-4 py-3 text-left">Tipe</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Referensi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $t->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($t->type) }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($t->amount,0,',','.') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($t->status) }}</td>
                                    <td class="px-4 py-3">{{ $t->reference_number }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Tidak ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
