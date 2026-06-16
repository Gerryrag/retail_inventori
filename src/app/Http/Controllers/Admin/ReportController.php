<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->latest()->get();
        $orders = Order::query()->with('user')->latest()->get();

        return view('admin.reports.index', [
            'products' => $products,
            'orders' => $orders,
            'movements' => StockMovement::query()->with('product')->latest()->limit(100)->get(),
            'inventoryValue' => $products->sum(fn (Product $product) => $product->price * $product->stock),
            'salesValue' => $orders->sum('total_price'),
        ]);
    }
}
