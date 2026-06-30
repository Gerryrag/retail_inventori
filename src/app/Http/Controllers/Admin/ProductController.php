<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CloudinaryUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()
                ->with('variants')
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request, CloudinaryUploader $uploader): RedirectResponse
    {
        $product = Product::create($this->productData($request, $uploader));

        foreach ($request->input('variants', []) as $variant) {
            if (! filled($variant['size'] ?? null)) {
                continue;
            }

            $product->variants()->create([
                'size' => strtoupper((string) $variant['size']),
                'sku' => ($variant['sku'] ?? null) ?: $product->sku.'-'.strtoupper((string) $variant['size']),
                'stock' => max(0, (int) ($variant['stock'] ?? 0)),
                'is_active' => (bool) ($variant['is_active'] ?? true),
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product, CloudinaryUploader $uploader): RedirectResponse
    {
        $product->update($this->productData($request, $uploader));

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function productData(Request $request, CloudinaryUploader $uploader): array
    {
        $data = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($request->route('product')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'weight_gram' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
            'variants' => ['nullable', 'array'],
            'variants.*.size' => ['nullable', 'string', 'max:24', 'distinct'],
            'variants.*.sku' => ['nullable', 'string', 'max:255', 'unique:product_variants,sku'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];

        if ($request->hasFile('image')) {
            $data['image_url'] = $uploader->uploadImage($request->file('image'));
        }

        unset($data['image'], $data['variants']);

        return $data;
    }
}
