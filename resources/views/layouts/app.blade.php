<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <style>
                /* Sidebar collapse styles */
                .app-sidebar { width: 16rem; transition: width 200ms ease; }
                .sidebar-collapsed .app-sidebar { width: 4.5rem; }
                .app-sidebar .label { transition: opacity 150ms ease, width 150ms; }
                .sidebar-collapsed .app-sidebar .label { opacity: 0; width: 0; overflow: hidden; display: none; }
                .app-sidebar .icon { transition: margin 150ms ease; }
                .sidebar-collapsed .app-sidebar .icon { margin-right: 0; }
            </style>

            <div class="flex">
                <!-- Sidebar (hidden on small screens) -->
                <aside class="hidden lg:block app-sidebar bg-white border-r shadow-sm">
                    <div class="p-3 flex items-center justify-between border-b">
                        <div class="flex items-center">
                            <button id="sidebar-collapse-toggle" class="p-1 rounded-md hover:bg-gray-100" title="Toggle sidebar">
                                <!-- hamburger / lines icon -->
                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            <span class="ml-3 font-semibold text-gray-700 label">Menu</span>
                        </div>
                    </div>
                    <nav class="p-4 space-y-2 sticky top-4">

    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">🏠</span>
        <span class="font-medium text-gray-700 label">Dashboard</span>
    </a>

    <a href="{{ route('transactions.topup') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">⬆️</span>
        <span class="font-medium text-gray-700 label">Top Up</span>
    </a>

    <a href="{{ route('transactions.transfer') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">🔁</span>
        <span class="font-medium text-gray-700 label">Transfer</span>
    </a>

    <a href="{{ route('transactions.withdraw') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">⬇️</span>
        <span class="font-medium text-gray-700 label">Withdraw</span>
    </a>

    <a href="{{ route('transactions.payment') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">🧾</span>
        <span class="font-medium text-gray-700 label">Pembayaran</span>
    </a>

    <a href="{{ route('transactions.history') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">📜</span>
        <span class="font-medium text-gray-700 label">Riwayat</span>
    </a>

    <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">👤</span>
        <span class="font-medium text-gray-700 label">Profil</span>
    </a>

    <a href="{{ route('smartfinance.index') }}" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-50 nav-item">
        <span class="mr-3 icon">📊</span>
        <span class="font-medium text-gray-700 label">Smart Finance</span>
    </a>

    <a href="{{ route('sales_forum.index') }}" class="flex items-center px-3 py-2 rounded-md bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 border-l-4 border-green-500 nav-item transition">
        <span class="mr-3 icon text-lg">🛍️</span>
        <span class="font-bold text-green-700 label">Forum Jual Beli</span>
    </a>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full text-left flex items-center px-3 py-2 rounded-md hover:bg-gray-50 text-red-600">
            <span class="mr-3 icon">🚪</span>
            <span class="font-medium label">Keluar</span>
        </button>
    </form>
</nav>




            
                </aside>

                <!-- Main content area -->
                <div class="flex-1">
                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main>
                        @yield('content')
                        {{ $slot }}

                    </main>
                </div>
            </div>
        </div>

    <script>
        // Desktop sidebar collapse toggle
        (function(){
            const collapseBtn = document.getElementById('sidebar-collapse-toggle');
            if(!collapseBtn) return;
            collapseBtn.addEventListener('click', function(){
                document.body.classList.toggle('sidebar-collapsed');
            });
        })();
    </script>

    </body>
</html>
