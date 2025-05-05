<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPOS Kasir') }} - Dashboard Inventori</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-purple-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-white text-xl font-bold">SPOS Kasir</span>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ route('inventory.dashboard') }}" class="bg-purple-700 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="{{ route('inventory.index') }}" class="text-white hover:bg-purple-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Inventori</a>
                                <a href="{{ route('products.index') }}" class="text-white hover:bg-purple-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Produk</a>
                                <a href="{{ route('categories.index') }}" class="text-white hover:bg-purple-500 hover:bg-opacity-75 px-3 py-2 rounded-md text-sm font-medium">Kategori</a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <div class="relative">
                                <div>
                                    <button type="button" class="max-w-xs bg-purple-600 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-purple-600 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-purple-700">
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
                        <button type="button" class="bg-purple-600 inline-flex items-center justify-center p-2 rounded-md text-purple-200 hover:text-white hover:bg-purple-500 hover:bg-opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-purple-600 focus:ring-white" id="mobile-menu-button">
                