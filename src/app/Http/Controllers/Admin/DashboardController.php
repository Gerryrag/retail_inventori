<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::query()
            ->with('variants')
            ->latest()
            ->get();

        $paidOrders = Order::query()->where('payment_status', 'paid');
        $monthlyRevenue = (clone $paidOrders)
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('grand_total');
        $monthlySales = Order::query()
            ->where('payment_status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->selectRaw('EXTRACT(MONTH FROM paid_at) as month, SUM(grand_total) as revenue')
            ->groupBy('month')
            ->pluck('revenue', 'month');
        $salesChart = collect(range(1, 12))->map(fn (int $month): array => [
            'label' => now()->month($month)->format('M'),
            'value' => (int) ($monthlySales[$month] ?? 0),
        ]);
        $maxSales = max(1, $salesChart->max('value'));

        return view('admin.dashboard', [
            'products' => $products,
            'totalProducts' => $products->count(),
            'totalVariants' => ProductVariant::query()->count(),
            'totalStock' => ProductVariant::query()->sum('stock'),
            'totalInventoryValue' => ProductVariant::query()
                ->with('product')
                ->get()
                ->sum(fn (ProductVariant $variant): int => $variant->stock * (int) $variant->product?->price),
            'lowStockVariants' => ProductVariant::query()->where('stock', '<=', 5)->count(),
            'lowStockList' => ProductVariant::query()
                ->with('product')
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->limit(4)
                ->get(),
            'monthlyRevenue' => $monthlyRevenue,
            'salesChart' => $salesChart,
            'maxSales' => $maxSales,
            'totalOrders' => Order::query()->count(),
            'activeChats' => ChatMessage::query()->distinct('sender_name')->count('sender_name'),
            'paidOrdersThisMonth' => (clone $paidOrders)->whereYear('paid_at', now()->year)->whereMonth('paid_at', now()->month)->count(),
            'recentOrders' => Order::query()->latest()->limit(6)->get(),
            'topVariants' => OrderItem::query()
                ->selectRaw('product_name, variant_size, SUM(quantity) as sold_qty, SUM(total_price) as revenue')
                ->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))
                ->groupBy('product_name', 'variant_size')
                ->orderByDesc('sold_qty')
                ->limit(5)
                ->get(),
            'totalMovements' => StockMovement::query()->count(),
            'unreadNotifications' => AdminNotification::query()->where('is_read', false)->count(),
            'totalMessages' => ChatMessage::query()->count(),
        ]);
    }
}
