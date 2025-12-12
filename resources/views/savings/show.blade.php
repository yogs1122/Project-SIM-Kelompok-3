<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $plan->title }} - Sado Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .progress-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
        .stats-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .transaction-item {
            border-left: 4px solid;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .deposit { border-color: #28a745; }
        .withdrawal { border-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Back Button -->
        <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary mb-4">
            ← Kembali ke Daftar
        </a>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Plan Header -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fs-2 me-3">{{ $plan->icon }}</span>
                            <div>
                                <h1 class="h3 mb-1">{{ $plan->title }}</h1>
                                <span class="badge" style="background-color: {{ $plan->color_code }}20; color: {{ $plan->color_code }};">
                                    {{ ucfirst($plan->category) }}
                                </span>
                                <span class="badge bg-{{ $plan->status == 'active' ? 'success' : ($plan->status == 'completed' ? 'info' : 'secondary') }} ms-2">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                        </div>
                        <p class="text-muted mb-0">{{ $plan->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Aksi
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('savings.edit', $plan->id) }}">
                                    ✏️ Edit Rencana
                                </a>
                            </li>
                            @if($plan->status == 'active')
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addFundsModal">
                                    💰 Tambah Dana
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('savings.destroy', $plan->id) }}" method="POST" id="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item text-danger" onclick="confirmDelete()">
                                        🗑️ Hapus Rencana
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Progress & Stats -->
            <div class="col-lg-8">
                <!-- Progress Section -->
                <div class="progress-container">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <div class="progress-circle" style="background: conic-gradient({{ $plan->color_code }} {{ $plan->progress_percentage }}%, #e9ecef 0%);">
                                <div style="background: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    {{ $plan->progress_percentage }}%
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">{{ $plan->days_left }} hari lagi</small>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Progress</span>
                                    <span class="fw-bold">{{ $plan->progress_percentage }}%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $plan->progress_percentage }}%; background-color: {{ $plan->color_code }};">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="stats-card" style="background-color: {{ $plan->color_code }}10;">
                                        <small class="text-muted d-block">Tersimpan</small>
                                        <h4 class="mb-0">Rp {{ number_format($plan->current_amount, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stats-card" style="background-color: {{ $plan->color_code }}10;">
                                        <small class="text-muted d-block">Target</small>
                                        <h4 class="mb-0">Rp {{ number_format($plan->target_amount, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-calendar"></i> Dibuat: {{ $plan->created_at->translatedFormat('d F Y') }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-clock"></i> Target Selesai: {{ \Carbon\Carbon::parse($plan->end_date)->translatedFormat('d F Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-muted mb-2">Perlu Ditabung/Bulan</div>
                                <h4 class="text-primary">Rp {{ number_format($plan->required_monthly, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-muted mb-2">Sisa yang Perlu Ditabung</div>
                                <h4 class="text-primary">Rp {{ number_format($plan->target_amount - $plan->current_amount, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="text-muted mb-2">Total Transaksi</div>
                                <h4 class="text-primary">{{ $stats['transaction_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Transaksi Terbaru</h5>
                        <a href="{{ route('savings.transactions', $plan->id) }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        @if(isset($plan->transactions) && $plan->transactions->count() > 0)
                            @foreach($plan->transactions->take(5) as $transaction)
                                <div class="transaction-item {{ $transaction->transaction_type }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ $transaction->transaction_type == 'deposit' ? '➕ Setoran' : '➖ Penarikan' }}
                                            </h6>
                                            <small class="text-muted">
                                                {{ $transaction->payment_method }} • 
                                                {{ $transaction->created_at->translatedFormat('d M Y H:i') }}
                                            </small>
                                            @if($transaction->notes)
                                                <p class="mb-0 small">{{ $transaction->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <h6 class="mb-1 {{ $transaction->transaction_type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}
                                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                            </h6>
                                            <small class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <span class="display-4 text-muted">📝</span>
                                <p class="text-muted mt-3">Belum ada transaksi</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Action & Info -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        @if($plan->status == 'active')
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFundsModal">
                                    💰 Tambah Dana
                                </button>
                                <a href="{{ route('savings.edit', $plan->id) }}" class="btn btn-outline-primary">
                                    ✏️ Edit Rencana
                                </a>
                                <a href="{{ route('savings.transactions', $plan->id) }}" class="btn btn-outline-secondary">
                                    📋 Lihat Transaksi
                                </a>
                            </div>
                        @elseif($plan->status == 'completed')
                            <div class="alert alert-success">
                                <h6>🎉 Target Tercapai!</h6>
                                <p class="mb-0">Selamat! Tabungan Anda sudah mencapai target.</p>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-success" disabled>
                                    ✅ Rencana Selesai
                                </button>
                            </div>
                        @else
                            <div class="alert alert-secondary">
                                <h6>⏸️ Rencana Dibatalkan</h6>
                                <p class="mb-0">Rencana tabungan ini telah dibatalkan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Plan Info -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Rencana</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><small class="text-muted">Status</small></td>
                                <td>
                                    <span class="badge bg-{{ $plan->status == 'active' ? 'success' : ($plan->status == 'completed' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><small class="text-muted">Mulai</small></td>
                                <td>{{ \Carbon\Carbon::parse($plan->start_date)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><small class="text-muted">Target Selesai</small></td>
                                <td>{{ \Carbon\Carbon::parse($plan->end_date)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><small class="text-muted">Sisa Hari</small></td>
                                <td>{{ $plan->days_left }} hari</td>
                            </tr>
                            <tr>
                                <td><small class="text-muted">Auto Save</small></td>
                                <td>{{ $plan->auto_save ? 'Aktif' : 'Tidak Aktif' }}</td>
                            </tr>
                            @if($plan->is_public)
                                <tr>
                                    <td><small class="text-muted">Visibilitas</small></td>
                                    <td><span class="badge bg-info">Publik</span></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Funds Modal -->
    <div class="modal fade" id="addFundsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('savings.add-funds', $plan->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Dana ke Tabungan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jumlah yang akan ditambahkan *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="amount" 
                                       min="1000" step="1000" required
                                       placeholder="Masukkan jumlah">
                            </div>
                            <small class="text-muted">Minimal Rp 1.000</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran *</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="">Pilih metode...</option>
                                <option value="wallet">Saldo Sado Wallet</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="credit_card">Kartu Kredit/Debit</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" name="notes" rows="2" 
                                      placeholder="Contoh: Setoran bulan Januari"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                Dana akan dipotong dari saldo wallet Anda. Pastikan saldo mencukupi.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirm delete
        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus rencana tabungan ini?')) {
                document.getElementById('deleteForm').submit();
            }
        }
        
        // Auto-dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.remove('show');
            });
        }, 5000);
    </script>
</body>
</html>