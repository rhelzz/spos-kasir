<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Disini adalah tempat untuk mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini akan dimuat oleh RouteServiceProvider dan semuanya akan
| ditetapkan ke grup middleware "web".
|
*/

// Rute publik
Route::get('/', function () {
    return view('welcome');
});

// Rute autentikasi
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kategori
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Produk
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Manajemen Inventori
    Route::middleware(['role:owner,inventory'])->group(function () {
        Route::get('/inventory', [InventoryItemController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{inventoryItem}', [InventoryItemController::class, 'show'])->name('inventory.show');
        Route::post('/inventory/{inventoryItem}/update-stock', [InventoryItemController::class, 'updateStock'])
            ->name('inventory.updateStock');
    });
    
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/inventory/create', [InventoryItemController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryItemController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{inventoryItem}/edit', [InventoryItemController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{inventoryItem}', [InventoryItemController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('inventory.destroy');
    });

    // Manajemen Pajak
    Route::middleware(['role:owner,cashier'])->group(function () {
        Route::get('/taxes', [TaxController::class, 'index'])->name('taxes.index');
    });
    
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/taxes/create', [TaxController::class, 'create'])->name('taxes.create');
        Route::post('/taxes', [TaxController::class, 'store'])->name('taxes.store');
        Route::get('/taxes/{tax}/edit', [TaxController::class, 'edit'])->name('taxes.edit');
        Route::put('/taxes/{tax}', [TaxController::class, 'update'])->name('taxes.update');
        Route::delete('/taxes/{tax}', [TaxController::class, 'destroy'])->name('taxes.destroy');
    });

    // Manajemen Diskon
    Route::middleware(['role:owner,cashier'])->group(function () {
        Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
    });
    
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/discounts/create', [DiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{discount}/edit', [DiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update');
        Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');
    });

    // Manajemen Meja
    Route::middleware(['role:owner,cashier'])->group(function () {
        Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
        Route::get('/tables/{table}', [TableController::class, 'show'])->name('tables.show');
        Route::patch('/tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.updateStatus');
    });
    
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/tables/create', [TableController::class, 'create'])->name('tables.create');
        Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
        Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
        Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
        Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
    });

    // Manajemen Pesanan
    Route::middleware(['role:owner,cashier'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    // Pemrosesan Pembayaran
    Route::middleware(['role:owner,cashier'])->group(function () {
        Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/print', [PaymentController::class, 'printReceipt'])->name('payments.print');
    });

    // Manajemen Pengguna (Hanya untuk role owner)
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Laporan
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });

    // Manajemen Shift Kasir
    Route::middleware(['role:owner,cashier'])->group(function () {
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts/open', [ShiftController::class, 'openShift'])->name('shifts.open');
        Route::post('/shifts/close', [ShiftController::class, 'closeShift'])->name('shifts.close');
        Route::get('/shifts/{shift}', [ShiftController::class, 'show'])->name('shifts.show');
    });
});