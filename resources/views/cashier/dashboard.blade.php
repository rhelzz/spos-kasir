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
                                <a href="{{ route('tables.index') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Meja</a>
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
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Profil Saya</a>
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
                    <a href="{{ route('tables.index') }}" class="text-white hover:bg-emerald-500 hover:bg-opacity-75 block px-3 py-2 rounded-md text-base font-medium">Meja</a>
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
                        <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-emerald-500 hover:bg-opacity-75">Profil Saya</a>
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
                    <h1 class="text-2xl font-semibold text-gray-900">Dashboard Kasir</h1>
                </div>

                <!-- Stats cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mb-6">
                    <!-- Card 1 -->
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

                    <!-- Card 2 -->
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

                <!-- Table Status and Quick Actions -->
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <!-- Table Status -->
                    <div class="lg:col-span-2 bg-white rounded-lg shadow">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Status Meja
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @forelse($tables ?? [] as $table)
                                    <div class="flex">
                                        <a href="{{ route('tables.show', $table->id) }}" class="flex-1 flex items-center justify-center py-3 px-2 text-sm font-medium rounded-md {{ $table->status === 'available' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $table->name }}
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

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Aksi Cepat
                            </h3>
                        </div>
                        <div class="p-4">
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('orders.create') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Pesanan Baru
                                </a>
                                <a href="{{ route('orders.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Daftar Pesanan
                                </a>
                                <a href="{{ route('shifts.open') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" 
                                   onclick="event.preventDefault(); document.getElementById('open-shift-form').submit();">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Buka Shift
                                </a>
                                <form id="open-shift-form" action="{{ route('shifts.open') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <a href="{{ route('shifts.close') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                   onclick="event.preventDefault(); document.getElementById('close-shift-form').submit();">
                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tutup Shift
                                </a>
                                <form id="close-shift-form" action="{{ route('shifts.close') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular Products -->
                <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Produk Populer
                        </h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            @forelse($popularProducts ?? [] as $product)
                                <button class="product-btn bg-gray-100 hover:bg-gray-200 rounded-md p-3 flex flex-col items-center justify-center text-center transition-all"
                                        data-id="{{ $product->id }}" 
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}">
                                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mb-2">
                                        <svg class="h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $product->name }}</span>
                                    <span class="text-xs text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </button>
                            @empty
                                <div class="col-span-full text-center text-gray-500 py-6">
                                    Tidak ada produk populer
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
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

        // Handle product button clicks
        document.querySelectorAll('.product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productName = this.dataset.name;
                const productPrice = this.dataset.price;
                
                // You can implement logic to add product to cart or redirect to order page
                console.log(`Product clicked: ${productName} (${productId}) - ${productPrice}`);
                window.location.href = `/orders/create?product=${productId}`;
            });
        });
    </script>
</body>
</html>