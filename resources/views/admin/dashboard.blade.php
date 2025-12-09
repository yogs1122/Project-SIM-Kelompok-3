<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex">
        <div class="w-64 bg-blue-800 text-white min-h-screen">
            <div class="p-6">
                <h1 class="text-2xl font-bold">E-Wallet Admin</h1>
                <p class="text-blue-200 mt-2">Panel Administrator</p>
            </div>
            
            <nav class="mt-8">
                <a href="/admin/dashboard" class="block py-3 px-6 bg-blue-900">
                    <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                </a>
                <a href="/admin/users" class="block py-3 px-6 hover:bg-blue-700">
                    <i class="fas fa-users mr-3"></i>Kelola User
                </a>
                <a href="/admin/transactions" class="block py-3 px-6 hover:bg-blue-700">
                    <i class="fas fa-exchange-alt mr-3"></i>Transaksi
                </a>
                <a href="/admin/umkm" class="block py-3 px-6 hover:bg-blue-700">
                    <i class="fas fa-store mr-3"></i>UMKM
                </a>
                <a href="/admin/analytics" class="block py-3 px-6 hover:bg-blue-700">
                    <i class="fas fa-chart-bar mr-3"></i>Analytics
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="block w-full text-left py-3 px-6 hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    </button>
                </form>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, Admin!</h1>
                <p class="text-gray-600">Dashboard Analytics & Monitoring</p>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Total User</p>
                            <h3 class="text-2xl font-bold">1,254</h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-4">
                            <i class="fas fa-exchange-alt text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Transaksi Hari Ini</p>
                            <h3 class="text-2xl font-bold">Rp 125 Jt</h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg mr-4">
                            <i class="fas fa-store text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">UMKM Aktif</p>
                            <h3 class="text-2xl font-bold">89</h3>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                            <i class="fas fa-chart-pie text-yellow-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500">Cluster User</p>
                            <h3 class="text-2xl font-bold">3</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Analytics Section -->
            <div class="bg-white p-6 rounded-xl shadow mb-8">
                <h2 class="text-xl font-bold mb-4">Analisis Clustering User</h2>
                <p class="text-gray-600 mb-4">Analisis statistik clustering K-Means berdasarkan perilaku transaksi</p>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="border border-blue-200 p-4 rounded-lg">
                        <h3 class="font-bold text-blue-700">Cluster 1: Premium</h3>
                        <p class="text-sm text-gray-600">Transaksi > Rp 5jt/bulan</p>
                        <p class="text-2xl font-bold mt-2">450 Users</p>
                    </div>
                    
                    <div class="border border-green-200 p-4 rounded-lg">
                        <h3 class="font-bold text-green-700">Cluster 2: Regular</h3>
                        <p class="text-sm text-gray-600">Transaksi Rp 1-5jt/bulan</p>
                        <p class="text-2xl font-bold mt-2">600 Users</p>
                    </div>
                    
                    <div class="border border-purple-200 p-4 rounded-lg">
                        <h3 class="font-bold text-purple-700">Cluster 3: Basic</h3>
                        <p class="text-sm text-gray-600">Transaksi < Rp 1jt/bulan</p>
                        <p class="text-2xl font-bold mt-2">204 Users</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>