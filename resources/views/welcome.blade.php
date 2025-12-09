<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Wallet UMKM - Sistem Keuangan Digital</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hero-bg {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-wallet text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <span class="text-xl font-bold text-gray-800">E-Wallet UMKM</span>
                        <span class="ml-2 text-sm text-blue-600 bg-blue-100 px-2 py-1 rounded">Statistika Project</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-bg text-white">
        <div class="max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl font-bold mb-6">
                    Sistem E-Wallet dengan 
                    <span class="text-blue-300">Analisis Statistika</span>
                </h1>
                <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Platform keuangan digital dengan fitur Smart Finance Analytics, 
                    Clustering User, Time Series Prediction, dan Marketplace UMKM lokal.
                </p>
                <div class="space-x-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white text-lg font-medium rounded-full hover:bg-blue-700">
                        <i class="fas fa-play-circle mr-3"></i> Mulai Sekarang
                    </a>
                    <a href="#features" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 text-lg font-medium rounded-full hover:bg-gray-100">
                        <i class="fas fa-info-circle mr-3"></i> Pelajari Fitur
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">
                🚀 Fitur Unggulan Sistem
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-blue-50 p-6 rounded-2xl">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Smart Finance Analytics</h3>
                    <p class="text-gray-600">
                        Analisis Time Series, Regresi Linear, dan prediksi keuangan dengan model statistika.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-green-50 p-6 rounded-2xl">
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-project-diagram text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">User Clustering</h3>
                    <p class="text-gray-600">
                        K-Means Clustering untuk segmentasi pengguna berdasarkan perilaku transaksi.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-purple-50 p-6 rounded-2xl">
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-store text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Marketplace UMKM</h3>
                    <p class="text-gray-600">
                        Platform jual-beli untuk UMKM lokal dengan sistem e-wallet terintegrasi.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-orange-50 p-6 rounded-2xl">
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Multi-Role System</h3>
                    <p class="text-gray-600">
                        ️ Login berbeda untuk Admin, User Biasa, dan Pedagang UMKM dengan dashboard khusus.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">📊 Analisis Statistika Terintegrasi</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">3</div>
                    <div class="text-blue-200">Model Analisis</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">85%</div>
                    <div class="text-blue-200">Akurasi Prediksi</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">3</div>
                    <div class="text-blue-200">Cluster User</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">24/7</div>
                    <div class="text-blue-200">Real-time Analytics</div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gray-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-6">Siap Mengelola Keuangan dengan Lebih Cerdas?</h2>
            <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                Bergabung dengan sistem e-wallet pertama dengan analisis statistika lengkap untuk mahasiswa.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-green-500 text-white text-lg font-medium rounded-full hover:bg-green-600">
                <i class="fas fa-user-plus mr-3"></i> Daftar Gratis Sekarang
            </a>
            
            <div class="mt-12 pt-8 border-t border-gray-700">
                <p class="text-gray-400">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    Project Tugas Akhir - Program Studi Statistika
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Mengimplementasikan Time Series Analysis, Linear Regression, dan K-Means Clustering dalam sistem e-wallet.
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex justify-center items-center mb-4">
                    <i class="fas fa-wallet text-3xl text-blue-400 mr-3"></i>
                    <span class="text-2xl font-bold">E-Wallet UMKM</span>
                </div>
                <p class="text-gray-400 mb-6">
                    Sistem Keuangan Digital dengan Analisis Statistika untuk UMKM Lokal
                </p>
                <div class="flex justify-center space-x-6 mb-6">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fas fa-book text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
                <p class="text-sm text-gray-500">
                    © 2024 E-Wallet UMKM Project. Dibuat dengan Laravel & Tailwind CSS.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>