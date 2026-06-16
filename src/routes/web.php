<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Retail\CheckoutController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('retail.landing', [
        'products' => Product::query()
            ->where('is_active', true)
            ->latest()
            ->get(),
    ]);
})->name('home');

Route::view('/login', 'auth.login')->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');

Route::middleware('admin')->group(function () {
    Route::get('/admin', DashboardController::class)->name('admin.dashboard');
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::get('/admin/stock', [StockMovementController::class, 'index'])->name('admin.stock.index');
    Route::post('/admin/stock', [StockMovementController::class, 'store'])->name('admin.stock.store');
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications', [NotificationController::class, 'store'])->name('admin.notifications.store');
    Route::patch('/admin/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::delete('/admin/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::get('/admin/chat', [ChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/admin/chat', [ChatController::class, 'store'])->name('admin.chat.store');
});

Route::middleware('customer')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
});

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
