<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CloudinaryUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()->latest()->get(),
        ]);
    }

    public function store(Request $request, CloudinaryUploader $uploader): RedirectResponse
    {
        Product::create($this->productData($request, $uploader));

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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];

        if ($request->hasFile('image')) {
            $data['image_url'] = $uploader->uploadImage($request->file('image'));
        }

        unset($data['image']);

        return $data;
    }
}
