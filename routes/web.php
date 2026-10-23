<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route di bawah ini wajib login
Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Menu Katalog Pelanggan
    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

    // Produk: daftar & stok rendah
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');

    // Master data: hanya Admin / Owner
    Route::middleware('can:manage-master')->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
    });

    // Detail produk
    Route::get('/products/{product}', [ProductController::class, 'show'])
        ->whereNumber('product')
        ->name('products.show');

    // Pelanggan
    Route::middleware('can:manage-customers')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['show']);
    });

    // Penjualan
    Route::middleware('can:manage-sales')->group(function () {
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    });

    // Pengguna / Administrasi
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Redirect route lama yang sudah tidak dipakai
    Route::redirect('/suppliers', '/dashboard');
    Route::redirect('/purchases', '/dashboard');
});