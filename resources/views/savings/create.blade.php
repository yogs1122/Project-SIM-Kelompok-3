<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Rencana Tabungan - Sado Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .category-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .category-option:hover, .category-option.active {
            border-color: #0d6efd;
            background-color: #f0f8ff;
        }
        .category-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary me-3">
                        ← Kembali
                    </a>
                    <h1 class="h3 mb-0">Buat Rencana Tabungan Baru</h1>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('savings.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Rencana *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="Contoh: Tabungan DP Rumah" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi (Opsional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Tuliskan tujuan tabungan Anda...">{{ old('description') }}</textarea>
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
                                               id="target_amount" name="target_amount" value="{{ old('target_amount') }}" 
                                               min="10000" step="1000" required>
                                    </div>
                                    @error('target_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimal Rp 10.000</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Target Tanggal Selesai *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label mb-3">Pilih Kategori *</label>
                                <div class="row g-3">
                                    @php
                                        $categories = [
                                            'electronics' => ['icon' => '💻', 'label' => 'Elektronik', 'color' => '#2196F3'],
                                            'vehicle' => ['icon' => '🚗', 'label' => 'Kendaraan', 'color' => '#FF9800'],
                                            'property' => ['icon' => '🏠', 'label' => 'Properti', 'color' => '#4CAF50'],
                                            'education' => ['icon' => '🎓', 'label' => 'Pendidikan', 'color' => '#9C27B0'],
                                            'vacation' => ['icon' => '✈️', 'label' => 'Liburan', 'color' => '#00BCD4'],
                                            'emergency' => ['icon' => '🚨', 'label' => 'Dana Darurat', 'color' => '#F44336'],
                                            'other' => ['icon' => '💰', 'label' => 'Lainnya', 'color' => '#607D8B'],
                                        ];
                                    @endphp
                                    
                                    @foreach($categories as $key => $category)
                                        <div class="col-4 col-md-3">
                                            <div class="category-option @if(old('category') == $key) active @endif" 
                                                 data-category="{{ $key }}" 
                                                 style="border-color: {{ old('category') == $key ? $category['color'] : '#e0e0e0' }}">
                                                <div class="category-icon">{{ $category['icon'] }}</div>
                                                <div class="small">{{ $category['label'] }}</div>
                                                <input type="radio" name="category" value="{{ $key }}" 
                                                       id="category_{{ $key }}" 
                                                       @if(old('category') == $key) checked @endif 
                                                       style="display: none;" required>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('category')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Hidden fields for color and icon -->
                            <input type="hidden" name="color_code" id="color_code" value="{{ old('color_code', '#1976D2') }}">
                            <input type="hidden" name="icon" id="icon" value="{{ old('icon', '💰') }}">

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('savings.index') }}" class="btn btn-outline-secondary me-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Buat Rencana Tabungan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category selection
        document.querySelectorAll('.category-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.category-option').forEach(opt => {
                    opt.classList.remove('active');
                    opt.style.borderColor = '#e0e0e0';
                });
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Get category data
                const category = this.dataset.category;
                const categoryData = {
                    'electronics': { color: '#2196F3', icon: '💻' },
                    'vehicle': { color: '#FF9800', icon: '🚗' },
                    'property': { color: '#4CAF50', icon: '🏠' },
                    'education': { color: '#9C27B0', icon: '🎓' },
                    'vacation': { color: '#00BCD4', icon: '✈️' },
                    'emergency': { color: '#F44336', icon: '🚨' },
                    'other': { color: '#607D8B', icon: '💰' }
                };
                
                // Update radio button
                document.getElementById(`category_${category}`).checked = true;
                
                // Update hidden fields
                document.getElementById('color_code').value = categoryData[category].color;
                document.getElementById('icon').value = categoryData[category].icon;
                
                // Update border color
                this.style.borderColor = categoryData[category].color;
            });
        });

        // Set minimum date for end date (tomorrow)
        document.getElementById('end_date').min = new Date(new Date().getTime() + 86400000).toISOString().split('T')[0];
    </script>
</body>
</html>