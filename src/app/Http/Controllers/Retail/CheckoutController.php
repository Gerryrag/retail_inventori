<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $order = DB::transaction(function () use ($data): Order {
            $product = Product::query()
                ->where('is_active', true)
                ->lockForUpdate()
                ->findOrFail($data['product_id']);

            if ($product->stock <= 0) {
                throw ValidationException::withMessages([
                    'product_id' => 'Stok produk sudah habis.',
                ]);
            }

            $product->decrement('stock');

            return Order::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => 1,
                'total_price' => $product->price,
                'status' => 'pending',
            ]);
        });

        session()->put('checkout.order_id', $order->id);

        return redirect()
            ->route('checkout.show')
            ->with('status', 'Checkout berhasil dibuat dan tersimpan di database.');
    }

    public function show(): View
    {
        return view('retail.checkout', [
            'order' => Order::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->first(),
        ]);
    }
}
