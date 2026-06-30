<?php

use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Webhooks\DokuWebhookController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'auth.login')->name('login');
Route::redirect('/login', '/');

Route::middleware('admin')->group(function () {
    Route::get('/admin', DashboardController::class)->name('admin.dashboard');
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/admin/products/{product}/variants', [ProductVariantController::class, 'store'])->name('admin.products.variants.store');
    Route::put('/admin/product-variants/{variant}', [ProductVariantController::class, 'update'])->name('admin.products.variants.update');
    Route::delete('/admin/product-variants/{variant}', [ProductVariantController::class, 'destroy'])->name('admin.products.variants.destroy');
    Route::get('/admin/stock', [StockMovementController::class, 'index'])->name('admin.stock.index');
    Route::post('/admin/stock', [StockMovementController::class, 'store'])->name('admin.stock.store');
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::post('/admin/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::post('/admin/orders/{order}/doku-payment', [OrderController::class, 'createDokuPayment'])->name('admin.orders.doku-payment');
    Route::patch('/admin/orders/{order}/shipment', [OrderController::class, 'updateShipment'])->name('admin.orders.shipment');
    Route::get('/admin/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('admin.orders.invoice');
    Route::get('/admin/orders/{order}/packing-slip', [OrderController::class, 'packingSlip'])->name('admin.orders.packing-slip');
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications', [NotificationController::class, 'store'])->name('admin.notifications.store');
    Route::patch('/admin/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::delete('/admin/notifications/{notification}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');
    Route::get('/admin/chat', [ChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/admin/chat', [ChatController::class, 'store'])->name('admin.chat.store');
});

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');
Route::post('/webhooks/doku', DokuWebhookController::class)->name('webhooks.doku');
