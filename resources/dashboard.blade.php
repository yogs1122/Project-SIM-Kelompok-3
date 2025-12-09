<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Selamat Datang, {{ Auth::user()->name }}!</h1>
                    
                    @if(Auth::user()->isUMKM())
                        <div class="bg-green-50 p-4 rounded-lg mb-6">
                            <h3 class="font-bold text-green-800">
                                <i class="fas fa-store mr-2"></i>Anda adalah Pedagang UMKM
                            </h3>
                            <p class="text-green-600 mt-2">Anda dapat menjual produk di marketplace kami.</p>
                        </div>
                    @endif
                    
                    <!-- Smart Finance Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4">Smart Finance Analytics</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Time Series Prediction -->
                            <div class="border border-blue-200 p-4 rounded-lg">
                                <h3 class="font-bold text-blue-700 mb-2">
                                    <i class="fas fa-chart-line mr-2"></i>Time Series Analysis
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">Prediksi pengeluaran bulan depan</p>
                                <p class="text-2xl font-bold text-blue-600">Rp 2.450.000</p>
                                <p class="text-xs text-gray-500 mt-1">Akurasi: 87%</p>
                            </div>
                            
                            <!-- Regression Analysis -->
                            <div class="border border-green-200 p-4 rounded-lg">
                                <h3 class="font-bold text-green-700 mb-2">
                                    <i class="fas fa-calculator mr-2"></i>Regression Model
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">Analisis regresi pengeluaran</p>
                                <div class="text-sm">
                                    <p>R² = 0.92</p>
                                    <p>y = 1.2x + 500000</p>
                                </div>
                            </div>
                            
                            <!-- Spending Pattern -->
                            <div class="border border-purple-200 p-4 rounded-lg">
                                <h3 class="font-bold text-purple-700 mb-2">
                                    <i class="fas fa-chart-pie mr-2"></i>Spending Pattern
                                </h3>
                                <p class="text-sm text-gray-600 mb-3">Pola pengeluaran Anda</p>
                                <ul class="text-sm">
                                    <li>• Makanan: 35%</li>
                                    <li>• Transport: 25%</li>
                                    <li>• Belanja: 40%</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h3 class="text-lg font-bold mb-4">Aksi Cepat</h3>
                        <div class="flex flex-wrap gap-4">
                            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-paper-plane mr-2"></i>Transfer
                            </a>
                            <a href="#" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>Top Up
                            </a>
                            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                                <i class="fas fa-store mr-2"></i>Marketplace
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>