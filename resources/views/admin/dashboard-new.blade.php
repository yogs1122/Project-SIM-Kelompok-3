@extends('layouts.app')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Selamat Datang, Admin!</h1>
            <p class="text-gray-600 mt-2">Dashboard Analytics & Management System</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total User</p>
                        <h3 class="text-3xl font-bold text-blue-700 mt-1">1,254</h3>
                        <p class="text-blue-600 text-xs mt-2">↑ 12% dari bulan lalu</p>
                    </div>
                    <div class="bg-blue-500 p-4 rounded-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Transaksi Hari Ini</p>
                        <h3 class="text-3xl font-bold text-green-700 mt-1">Rp 125 Jt</h3>
                        <p class="text-green-600 text-xs mt-2">847 transaksi</p>
                    </div>
                    <div class="bg-green-500 p-4 rounded-lg">
                        <i class="fas fa-exchange-alt text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">UMKM Aktif</p>
                        <h3 class="text-3xl font-bold text-purple-700 mt-1">89</h3>
                        <p class="text-purple-600 text-xs mt-2">↑ 8% dari bulan lalu</p>
                    </div>
                    <div class="bg-purple-500 p-4 rounded-lg">
                        <i class="fas fa-store text-white text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-xl shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">User Cluster</p>
                        <h3 class="text-3xl font-bold text-yellow-700 mt-1">3</h3>
                        <p class="text-yellow-600 text-xs mt-2">Premium, Regular, Basic</p>
                    </div>
                    <div class="bg-yellow-500 p-4 rounded-lg">
                        <i class="fas fa-chart-pie text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="#" class="p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-users-cog text-blue-600 text-2xl"></i>
                    <h3 class="text-lg font-bold text-gray-800 ml-4">Kelola User</h3>
                </div>
                <p class="text-gray-600 text-sm">Manage semua pengguna dan role mereka</p>
            </a>

            <a href="#" class="p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exchange-alt text-green-600 text-2xl"></i>
                    <h3 class="text-lg font-bold text-gray-800 ml-4">Transaksi</h3>
                </div>
                <p class="text-gray-600 text-sm">Monitor semua transaksi sistem</p>
            </a>

            <a href="#" class="p-6 bg-white border border-gray-200 rounded-lg hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <i class="fas fa-store text-purple-600 text-2xl"></i>
                    <h3 class="text-lg font-bold text-gray-800 ml-4">UMKM</h3>
                </div>
                <p class="text-gray-600 text-sm">Kelola pedagang UMKM terdaftar</p>
            </a>
        </div>

        <!-- Analytics Section -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Analisis User Clustering</h2>
            <p class="text-gray-600 mb-6">Segmentasi user berdasarkan K-Means Clustering dari perilaku transaksi</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border-l-4 border-blue-500 p-4 bg-blue-50 rounded">
                    <h4 class="font-bold text-blue-900 mb-2">Cluster 1: Premium</h4>
                    <p class="text-sm text-gray-700 mb-3">Transaksi > Rp 5 juta/bulan</p>
                    <div class="flex items-baseline">
                        <p class="text-3xl font-bold text-blue-700">450</p>
                        <p class="text-gray-600 ml-2 text-sm">users (35.8%)</p>
                    </div>
                </div>

                <div class="border-l-4 border-green-500 p-4 bg-green-50 rounded">
                    <h4 class="font-bold text-green-900 mb-2">Cluster 2: Regular</h4>
                    <p class="text-sm text-gray-700 mb-3">Transaksi Rp 1-5 juta/bulan</p>
                    <div class="flex items-baseline">
                        <p class="text-3xl font-bold text-green-700">600</p>
                        <p class="text-gray-600 ml-2 text-sm">users (47.8%)</p>
                    </div>
                </div>

                <div class="border-l-4 border-orange-500 p-4 bg-orange-50 rounded">
                    <h4 class="font-bold text-orange-900 mb-2">Cluster 3: Basic</h4>
                    <p class="text-sm text-gray-700 mb-3">Transaksi < Rp 1 juta/bulan</p>
                    <div class="flex items-baseline">
                        <p class="text-3xl font-bold text-orange-700">204</p>
                        <p class="text-gray-600 ml-2 text-sm">users (16.2%)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Transaksi Terbaru</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Tipe</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-700">Jumlah</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">Budi Santoso</td>
                            <td class="py-3 px-4"><span class="text-blue-600 font-medium">Transfer</span></td>
                            <td class="py-3 px-4 text-right">Rp 500.000</td>
                            <td class="py-3 px-4"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Success</span></td>
                            <td class="py-3 px-4 text-gray-600">2 jam lalu</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">Siti Nurhaliza</td>
                            <td class="py-3 px-4"><span class="text-green-600 font-medium">Top Up</span></td>
                            <td class="py-3 px-4 text-right">Rp 1.000.000</td>
                            <td class="py-3 px-4"><span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Success</span></td>
                            <td class="py-3 px-4 text-gray-600">5 jam lalu</td>
                        </tr>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">Ahmad Wijaya</td>
                            <td class="py-3 px-4"><span class="text-orange-600 font-medium">Withdraw</span></td>
                            <td class="py-3 px-4 text-right">Rp 750.000</td>
                            <td class="py-3 px-4"><span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Pending</span></td>
                            <td class="py-3 px-4 text-gray-600">8 jam lalu</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
