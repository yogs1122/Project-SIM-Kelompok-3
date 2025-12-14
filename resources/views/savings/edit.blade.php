<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $plan->title }} - Sado Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('savings.show', $plan->id) }}" class="btn btn-outline-secondary me-3">
                ← Kembali
            </a>
            <h1 class="h3 mb-0">Edit Rencana Tabungan</h1>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('savings.update', $plan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Rencana *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $plan->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $plan->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="target_amount" class="form-label">Target Jumlah *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('target_amount') is-invalid @enderror" 
                                               id="target_amount" name="target_amount" 
                                               value="{{ old('target_amount', $plan->target_amount) }}" 
                                               min="10000" step="1000" required>
                                    </div>
                                    @error('target_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Target Tanggal Selesai *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $plan->end_date->format('Y-m-d')) }}" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori *</label>
                                <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                    <option value="electronics" {{ old('category', $plan->category) == 'electronics' ? 'selected' : '' }}>Elektronik</option>
                                    <option value="vehicle" {{ old('category', $plan->category) == 'vehicle' ? 'selected' : '' }}>Kendaraan</option>
                                    <option value="property" {{ old('category', $plan->category) == 'property' ? 'selected' : '' }}>Properti</option>
                                    <option value="education" {{ old('category', $plan->category) == 'education' ? 'selected' : '' }}>Pendidikan</option>
                                    <option value="vacation" {{ old('category', $plan->category) == 'vacation' ? 'selected' : '' }}>Liburan</option>
                                    <option value="emergency" {{ old('category', $plan->category) == 'emergency' ? 'selected' : '' }}>Dana Darurat</option>
                                    <option value="other" {{ old('category', $plan->category) == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $plan->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="completed" {{ old('status', $plan->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ old('status', $plan->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('savings.show', $plan->id) }}" class="btn btn-outline-secondary me-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>