<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi {{ $plan->title }} - Sado Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container py-4">
        <!-- Back Button -->
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('savings.show', $plan->id) }}" class="btn btn-outline-secondary me-3">
                ← Kembali ke Detail
            </a>
            <h1 class="h4 mb-0">Transaksi: {{ $plan->title }}</h1>
        </div>

        <!-- Summary -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Total Setoran</small>
                        <h4 class="text-success mb-0">
                            Rp {{ number_format($transactions->where('transaction_type', 'deposit')->sum('amount'), 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Total Penarikan</small>
                        <h4 class="text-danger mb-0">
                            Rp {{ number_format($transactions->where('transaction_type', 'withdrawal')->sum('amount'), 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Jumlah Transaksi</small>
                        <h4 class="mb-0">{{ $transactions->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->translatedFormat('d M Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->transaction_type == 'deposit' ? 'success' : 'danger' }}">
                                                {{ $transaction->transaction_type == 'deposit' ? 'Setoran' : 'Penarikan' }}
                                            </span>
                                        </td>
                                        <td class="{{ $transaction->transaction_type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </td>
                                        <td>{{ ucfirst($transaction->payment_method) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($transactions->hasPages())
                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-receipt display-1 text-muted"></i>
                        <h4 class="mt-3">Belum ada transaksi</h4>
                        <p class="text-muted">Belum ada riwayat transaksi untuk rencana ini.</p>
                        <a href="{{ route('savings.show', $plan->id) }}" class="btn btn-primary">
                            Kembali ke Detail
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>