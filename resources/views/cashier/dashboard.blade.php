<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPOS Kasir') }} - Dashboard Kasir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-emerald-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-white text-xl font-bold">SPOS Kasir</span>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ route('cashier.dashboard') }}" class="bg-emerald-700 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="{{ route('orders.create') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Pesanan Baru</a>
                                <a href="{{ route('orders.index') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Daftar Pesanan</a>
                                <a href="{{ route('products.index') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Produk</a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <div class="relative">
                                <div>
                                    <button type="button" class="max-w-xs bg-emerald-600 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-emerald-600 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-700">
                                            <span class="text-sm font-medium leading-none text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </span>
                                    </button>
                                </div>
                                <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" id="user-menu">
                                    <span class="block px-4 py-2 text-sm text-gray-700">{{ Auth::user()->name }}</span>
                                    <span class="block px-4 py-2 text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</span>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Profil Saya</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <button type="button" class="bg-emerald-600 inline-flex items-center justify-center p-2 rounded-md text-emerald-200 hover:text-white hover:bg-emerald-500 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-emerald-600 focus:ring-white" id="mobile-menu-button">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:hidden hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('cashier.dashboard') }}" class="bg-emerald-700 text-white block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                    <a href="{{ route('orders.create') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block px-3 py-2 rounded-md text-base font-medium">Pesanan Baru</a>
                    <a href="{{ route('orders.index') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block px-3 py-2 rounded-md text-base font-medium">Daftar Pesanan</a>
                    <a href="{{ route('products.index') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block px-3 py-2 rounded-md text-base font-medium">Produk</a>
                </div>
                <div class="pt-4 pb-3 border-t border-emerald-700">
                    <div class="flex items-center px-5">
                        <div class="flex-shrink-0">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-700">
                                <span class="text-sm font-medium leading-none text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </span>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-white">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-emerald-300">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 px-2 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-emerald-500 hover:bg-opacity-75">Profil Saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-emerald-500 hover:bg-opacity-75">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Page header -->
                <div class="px-4 py-6 sm:px-0">
                    <div class="flex justify-between items-center">
                        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Kasir</h1>
                        <div class="flex space-x-2">
                            <!-- Current Date Display -->
                            <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-md shadow-sm text-gray-700 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
                            </div>
                            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Pesanan Baru
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <!-- Card 1: My Orders Today -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-emerald-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Pesanan Saya Hari Ini
                                        </dt>
                                        <dd>
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ $myOrdersToday ?? 0 }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: My Revenue Today -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Pendapatan Saya Hari Ini
                                        </dt>
                                        <dd>
                                            <div class="text-lg font-bold text-gray-900">
                                                Rp {{ number_format($myRevenueToday ?? 0, 0, ',', '.') }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Pending Orders -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-amber-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Pesanan Menunggu
                                        </dt>
                                        <dd>
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ $pendingOrders ?? 0 }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Completed Orders Today -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Pesanan Selesai Hari Ini
                                        </dt>
                                        <dd>
                                            <div class="text-lg font-bold text-gray-900">
                                                {{ $completedOrders ?? 0 }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Status and Recent Orders -->
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3 mb-6">
                    <!-- Table Status -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Status Meja
                            </h3>
                            <a href="{{ route('tables.index') }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @forelse($tables ?? [] as $table)
                                    <div class="flex">
                                        <a href="{{ route('orders.create', ['table_id' => $table->id]) }}" class="flex-1 flex items-center justify-center py-3 px-2 text-sm font-medium rounded-md 
                                            {{ $table->status === 'available' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}
                                            hover:bg-opacity-75 transition-colors duration-150">
                                            {{ $table->name }}
                                            <span class="ml-1 text-xs">
                                                {{ $table->status === 'available' ? '(Kosong)' : '(Terisi)' }}
                                            </span>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-gray-500 py-6">
                                        Tidak ada data meja
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Pesanan Terbaru
                            </h3>
                            <a href="{{ route('orders.index') }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="p-4 overflow-y-auto" style="max-height: 300px;">
                            <ul class="divide-y divide-gray-200">
                                @forelse($recentOrders ?? [] as $order)
                                    <li class="py-3">
                                        <a href="{{ route('orders.show', $order->id) }}" class="block hover:bg-gray-50">
                                            <div class="flex justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-emerald-600 truncate">
                                                        #{{ $order->id }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $order->created_at->format('d M Y H:i') }}
                                                    </p>
                                                    <p class="text-xs text-gray-700 mt-1">
                                                        {{ $order->table ? $order->table->name : 'Tanpa Meja' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full
                                                        {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $order->status == 'completed' ? 'Selesai' : 'Pending' }}
                                                    </span>
                                                    <p class="text-sm font-medium text-gray-900 text-right mt-1">
                                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="py-4 text-center text-gray-500">
                                        Tidak ada pesanan terbaru
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Popular Products -->
                <div class="bg-white shadow overflow-hidden rounded-lg">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Produk Populer
                        </h3>
                        <a href="{{ route('products.index') }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                            Semua Produk
                        </a>
                    </div>
                    <div class="bg-white p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @forelse($popularProducts ?? [] as $product)
                                <div class="relative group">
                                    <button onclick="addToOrder({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})" 
                                            class="w-full bg-white border rounded-lg overflow-hidden shadow hover:shadow-md transition-shadow duration-300">
                                        <div class="h-24 bg-gray-100 flex items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <div class="p-4 text-center">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h3>
                                            <p class="mt-1 text-sm font-bold text-emerald-600">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                Stok: {{ $product->stock }}
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-10 text-gray-500">
                                    Tidak ada data produk populer
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Aksi Cepat
                        </h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Pesanan Baru
                            </a>
                            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Daftar Pesanan
                            </a>
                            <a href="{{ route('tables.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Daftar Meja
                            </a>
                            <!-- Open shift button -->
                            <form action="{{ route('shifts.open') }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Buka Shift
                                </button>
                            </form>
                            <!-- Close shift button -->
                            <form action="{{ route('shifts.close') }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tutup Shift
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add to order modal -->
    <div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Tambahkan ke Pesanan</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <p id="modalProductName" class="text-gray-700 font-medium"></p>
                <p id="modalProductPrice" class="text-emerald-600 font-bold"></p>
            </div>
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                <div class="flex items-center">
                    <button type="button" onclick="decrementQuantity()" class="p-2 bg-gray-200 rounded-l-md">
                        <svg class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="p-2 w-16 text-center border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" />
                    <button type="button" onclick="incrementQuantity()" class="p-2 bg-gray-200 rounded-r-md">
                        <svg class="h-5 w-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea id="notes" rows="2" class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="button" onclick="proceedWithOrder()" class="px-4 py-2 bg-emerald-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-emerald-700">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
        // Toggle dropdown menu
        document.getElementById('user-menu-button')?.addEventListener('click', function() {
            document.getElementById('user-menu').classList.toggle('hidden');
        });

        // Toggle mobile menu
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Hide dropdown when clicking elsewhere
        window.addEventListener('click', function(e) {
            if (!document.getElementById('user-menu-button')?.contains(e.target)) {
                document.getElementById('user-menu')?.classList.add('hidden');
            }
        });

        // Product order modal
        let selectedProductId = null;
        let selectedProductName = null;
        let selectedProductPrice = null;

        function addToOrder(productId, productName, productPrice) {
            selectedProductId = productId;
            selectedProductName = productName;
            selectedProductPrice = productPrice;
            
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('modalProductPrice').textContent = 'Rp ' + productPrice.toLocaleString('id-ID');
            document.getElementById('quantity').value = 1;
            document.getElementById('notes').value = '';
            
            document.getElementById('orderModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        function incrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }

        function decrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }

        function proceedWithOrder() {
            const quantity = document.getElementById('quantity').value;
            const notes = document.getElementById('notes').value;
            
            // Redirect to the create order page with the product details
            window.location.href = `/orders/create?product_id=${selectedProductId}&quantity=${quantity}&notes=${encodeURIComponent(notes)}`;
        }
    </script>
</body>
</html>