<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;

// Auth Routes (Guest)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (Auth Required)
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard — ringkasan untuk semua role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route khusus harus didaftarkan sebelum slot dinamis {product}
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])
        ->name('products.low-stock');

    // CRUD master data + kategori + pengguna: Admin/Owner saja
    Route::middleware('can:manage-master')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Master data — semua role boleh MELIHAT produk & pemasok
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    Route::resource('suppliers', SupplierController::class)->only(['index']);

    // CRUD pemasok & kategori: Admin/Owner saja
    Route::middleware('can:manage-master')->group(function () {
        Route::resource('categories', CategoryController::class);

        Route::resource('suppliers', SupplierController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // Pembelian (stock-in): Admin & Staff dapat membuat; pembatalan hanya Admin
    Route::resource('purchases', PurchaseController::class);

    // Penjualan (stock-out): sesuai contoh arsitektur UTS — dikelola via gate manage-sales
    Route::middleware('can:manage-sales')->group(function () {
        Route::resource('sales', SaleController::class);
    });

    // Pembatalan transaksi: hanya Admin/Owner
    Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])
        ->name('purchases.cancel')->middleware('can:cancel-transaction');
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])
        ->name('sales.cancel')->middleware('can:cancel-transaction');

    // Pelanggan: Admin & Staff
    Route::resource('customers', CustomerController::class);

    // Pengguna & role: Admin/Owner
    Route::resource('users', UserController::class)->middleware('can:manage-users');
});
