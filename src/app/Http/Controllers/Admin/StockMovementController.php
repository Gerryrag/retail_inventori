<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(): View
    {
        return view('admin.stock.index', [
            'variants' => ProductVariant::query()
                ->with('product')
                ->whereHas('product')
                ->join('products', 'products.id', '=', 'product_variants.product_id')
                ->orderBy('products.name')
                ->orderBy('product_variants.size')
                ->select('product_variants.*')
                ->get(),
            'movements' => StockMovement::query()->with(['product', 'variant'])->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'type' => ['required', 'in:in,out,correction'],
            'quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data): void {
            $variant = ProductVariant::query()
                ->with('product')
                ->lockForUpdate()
                ->findOrFail($data['product_variant_id']);

            $stockBefore = $variant->stock;
            $quantity = (int) $data['quantity'];

            $stockAfter = match ($data['type']) {
                'in' => $stockBefore + $quantity,
                'out' => $stockBefore - $quantity,
                'correction' => $quantity,
            };

            if ($stockAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok varian ukuran tidak cukup untuk barang keluar.',
                ]);
            }

            $variant->update(['stock' => $stockAfter]);

            StockMovement::create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'type' => $data['type'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'note' => $data['note'] ?? null,
            ]);
        });

        return redirect()->route('admin.stock.index')->with('status', 'Pencatatan stok berhasil disimpan.');
    }
}
