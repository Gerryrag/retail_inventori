<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'size' => [
                'required',
                'string',
                'max:24',
                Rule::unique('product_variants', 'size')->where('product_id', $product->id),
            ],
            'sku' => ['required', 'string', 'max:255', 'unique:product_variants,sku'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];

        $data['size'] = strtoupper($data['size']);

        $product->variants()->create($data);

        return redirect()->route('admin.products.index')->with('status', 'Varian ukuran berhasil ditambahkan.');
    }

    public function update(Request $request, ProductVariant $variant): RedirectResponse
    {
        $data = $request->validate([
            'size' => [
                'required',
                'string',
                'max:24',
                Rule::unique('product_variants', 'size')
                    ->where('product_id', $variant->product_id)
                    ->ignore($variant),
            ],
            'sku' => ['required', 'string', 'max:255', Rule::unique('product_variants', 'sku')->ignore($variant)],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];

        $data['size'] = strtoupper($data['size']);
        $variant->update($data);

        return redirect()->route('admin.products.index')->with('status', 'Varian ukuran berhasil diperbarui.');
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $variant->delete();

        return redirect()->route('admin.products.index')->with('status', 'Varian ukuran berhasil dihapus.');
    }
}
