<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
            'products' => Product::query()->orderBy('name')->get(),
            'movements' => StockMovement::query()->with('product')->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:in,out,correction'],
            'quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data): void {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);
            $stockBefore = $product->stock;
            $quantity = (int) $data['quantity'];

            $stockAfter = match ($data['type']) {
                'in' => $stockBefore + $quantity,
                'out' => $stockBefore - $quantity,
                'correction' => $quantity,
            };

            if ($stockAfter < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak cukup untuk barang keluar.',
                ]);
            }

            $product->update(['stock' => $stockAfter]);

            StockMovement::create([
                'product_id' => $product->id,
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
