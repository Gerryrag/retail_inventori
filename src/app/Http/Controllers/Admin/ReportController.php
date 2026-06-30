<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->with('variants')->latest()->get();
        $paidOrders = Order::query()->with(['items', 'shipment'])->where('payment_status', 'paid')->latest()->get();

        return view('admin.reports.index', [
            'products' => $products,
            'paidOrders' => $paidOrders,
            'movements' => StockMovement::query()->with(['product', 'variant'])->latest()->limit(100)->get(),
            'inventoryValue' => ProductVariant::query()
                ->with('product')
                ->get()
                ->sum(fn (ProductVariant $variant): int => $variant->stock * (int) $variant->product?->price),
            'monthlyRevenue' => Order::query()
                ->where('payment_status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('grand_total'),
            'topVariants' => OrderItem::query()
                ->selectRaw('product_name, variant_size, SUM(quantity) as sold_qty, SUM(total_price) as revenue')
                ->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))
                ->groupBy('product_name', 'variant_size')
                ->orderByDesc('sold_qty')
                ->limit(10)
                ->get(),
        ]);
    }
}
