<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::query()
            ->latest()
            ->get();

        $orders = Order::query()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'products' => $products,
            'orders' => $orders,
            'totalProducts' => $products->count(),
            'totalStock' => $products->sum('stock'),
            'totalInventoryValue' => $products->sum(fn (Product $product) => $product->price * $product->stock),
            'lowStockProducts' => $products->where('stock', '<=', 10)->count(),
            'totalOrders' => Order::query()->count(),
            'totalMovements' => StockMovement::query()->count(),
            'unreadNotifications' => AdminNotification::query()->where('is_read', false)->count(),
            'totalMessages' => ChatMessage::query()->count(),
        ]);
    }
}
