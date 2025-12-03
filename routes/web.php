<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ReportController;

// --- AREA PUBLIK (Tamu/Guest) ---

Route::get('/', [MenuController::class, 'index'])->name('home');
// API STOK (Route Baru)
Route::get('/api/menu/stocks', [MenuController::class, 'getMenuStocks'])->name('api.menu.stocks');

// Keranjang & Checkout
Route::get('/cart', [MenuController::class, 'viewCart'])->name('view_cart');
Route::post('/checkout', [MenuController::class, 'checkout'])->name('checkout');

// PERBAIKAN: Ubah get menjadi post
Route::post('/add-to-cart/{id}', [MenuController::class, 'addToCart'])->name('add_to_cart');

// TAMBAHAN BARU (Gunakan POST biar aman)
Route::post('/decrease-item/{id}', [MenuController::class, 'decreaseItem'])->name('decrease_item');


Route::get('/clear-cart', [MenuController::class, 'clearCart'])->name('clear_cart');
// TAMBAHAN BARU: Hapus per item
Route::get('/remove-from-cart/{id}', [MenuController::class, 'removeFromCart'])->name('remove_from_cart');
// ...

// --- AUTHENTICATION (Login/Logout) ---

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- AREA TERBATAS (Middleware) ---

// 1. AREA OPERASIONAL / DAPUR
// Siapa yang boleh masuk? => Kasir, Admin, DAN Owner.
// (Admin/Owner perlu akses ini buat pantau dapur atau input order manual)
Route::middleware(['auth', 'role:cashier,admin,owner'])->group(function () {
    
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/order/{id}/update', [AuthController::class, 'updateStatus'])->name('order.update');

    // TAMBAHAN BARU: Route Cetak Struk
    Route::get('/order/{id}/print', [\App\Http\Controllers\AuthController::class, 'printReceipt'])->name('order.print');

    // ... existing routes ...
    
    // API AJAX (Route Baru)
    Route::get('/api/orders', [\App\Http\Controllers\AuthController::class, 'getNewOrders'])->name('api.orders');

});


// 2. AREA KANTOR / ADMIN PANEL
// Siapa yang boleh masuk? => HANYA Admin DAN Owner.
// (Kasir TIDAK BOLEH masuk sini)
Route::middleware(['auth', 'role:admin,owner'])->group(function () {

    // Dashboard Admin
    Route::get('admin/menus/trash', [\App\Http\Controllers\AdminMenuController::class, 'trash'])->name('admin.menus.trash');
    Route::get('admin/menus/{id}/restore', [\App\Http\Controllers\AdminMenuController::class, 'restore'])->name('admin.menus.restore');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('admin/menus', \App\Http\Controllers\AdminMenuController::class, ['as' => 'admin']);
    // Manajemen Menu
    Route::resource('admin/menus', AdminMenuController::class, ['as' => 'admin']);

    // Laporan Keuangan & AI
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');

    // TAMBAHAN BARU: EXPORT EXCEL
    Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');

    // TAMBAHAN BARU: KELOLA MEJA
    Route::resource('admin/tables', \App\Http\Controllers\TableController::class, ['as' => 'admin'])->only(['index', 'store', 'destroy']);



});