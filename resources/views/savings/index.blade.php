<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rencana Tabungan - Sado Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .saving-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .saving-card:hover {
            transform: translateY(-5px);
        }
        .progress-bar {
            height: 10px;
            border-radius: 5px;
        }
        .category-badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">📊 Rencana Tabungan</h1>
            <a href="{{ route('savings.create') }}" class="btn btn-primary">
                + Buat Rencana Baru
            </a>
        </div>

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

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Tersimpan</h6>
                        <h3 class="mb-0">Rp {{ number_format($stats['total_savings'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Target</h6>
                        <h3 class="mb-0">Rp {{ number_format($stats['total_targets'] ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Progress Keseluruhan</h6>
                        <h3 class="mb-0">{{ $stats['overall_progress'] ?? 0 }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Rencana Aktif</h6>
                        <h3 class="mb-0">{{ $stats['active_plans'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saving Plans List -->
        <div class="row">
            @forelse($plans as $plan)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card saving-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="fs-4">{{ $plan->icon }}</span>
                                    <h5 class="card-title mb-1">{{ $plan->title }}</h5>
                                    <span class="badge category-badge" style="background-color: {{ $plan->color_code }}20; color: {{ $plan->color_code }};">
                                        {{ ucfirst($plan->category) }}
                                    </span>
                                </div>
                                <span class="badge bg-{{ $plan->status == 'active' ? 'success' : ($plan->status == 'completed' ? 'info' : 'secondary') }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                            
                            <p class="text-muted small mb-3">{{ $plan->description ?? 'Tidak ada deskripsi' }}</p>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Progress: {{ $plan->progress_percentage }}%</small>
                                    <small class="text-muted">{{ $plan->days_left }} hari lagi</small>
                                </div>
                                <div class="progress progress-bar">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $plan->progress_percentage }}%; background-color: {{ $plan->color_code }};"
                                         aria-valuenow="{{ $plan->progress_percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <small>Tersimpan:</small>
                                <small class="fw-bold">Rp {{ number_format($plan->current_amount, 0, ',', '.') }}</small>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <small>Target:</small>
                                <small class="fw-bold">Rp {{ number_format($plan->target_amount, 0, ',', '.') }}</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('savings.show', $plan->id) }}" class="btn btn-outline-primary btn-sm">
                                    Lihat Detail
                                </a>
                                @if($plan->status == 'active')
                                    <a href="{{ route('savings.add-funds', $plan->id) }}" class="btn btn-primary btn-sm">
                                        + Tambah Dana
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <span class="display-1">🏦</span>
                        </div>
                        <h4 class="mb-3">Belum ada rencana tabungan</h4>
                        <p class="text-muted mb-4">Mulai buat rencana tabungan pertama Anda untuk mencapai tujuan finansial</p>
                        <a href="{{ route('savings.create') }}" class="btn btn-primary btn-lg">
                            + Buat Rencana Pertama
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.classList.remove('show');
            });
        }, 5000);
    </script>
</body>
</html>
