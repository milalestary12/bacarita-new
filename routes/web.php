<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SupplierController;

// Authentication Routes
Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.submit');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Kasir dan Admin Routes
    Route::middleware(['kasir'])->group(function () {
        // Buku Routes
        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
        
        // Penjualan Routes
        Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('/penjualan/export-pdf', [PenjualanController::class, 'exportPdf'])->name('penjualan.exportPdf');
    });
    
    // Admin-only Routes
    Route::middleware(['admin'])->group(function () {
        // Kategori Routes
        Route::resource('kategori', KategoriController::class);
        
        // Buku Admin Routes
        Route::resource('buku', BukuController::class)->except(['index', 'show']);
        
        // Supplier Routes
        Route::resource('supplier', SupplierController::class);
        
        // Pembelian Routes
        Route::resource('pembelian', PembelianController::class);
        Route::post('/pembelian/{pembelian}/konfirmasi', [PembelianController::class, 'konfirmasi'])->name('pembelian.konfirmasi');
        Route::get('/pembelian/export-pdf', [PembelianController::class, 'exportPdf'])->name('pembelian.exportPdf');
    });
});