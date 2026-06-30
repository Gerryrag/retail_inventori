<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = [
            [
                'sku' => 'OM-TSHIRT-LOGO',
                'name' => 'Official Logo T-Shirt',
                'description' => 'Kaos cotton combed dengan logo official brand.',
                'category' => 'T-Shirt',
                'price' => 149000,
                'weight_gram' => 250,
                'image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
                'variants' => ['S' => 12, 'M' => 24, 'L' => 18, 'XL' => 9],
            ],
            [
                'sku' => 'OM-HOODIE-CLASSIC',
                'name' => 'Classic Brand Hoodie',
                'description' => 'Hoodie fleece official merchandise.',
                'category' => 'Hoodie',
                'price' => 329000,
                'weight_gram' => 650,
                'image_url' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=800&q=80',
                'variants' => ['M' => 8, 'L' => 10, 'XL' => 6],
            ],
            [
                'sku' => 'OM-CAP-SIGNATURE',
                'name' => 'Signature Cap',
                'description' => 'Topi official dengan bordir logo.',
                'category' => 'Cap',
                'price' => 99000,
                'weight_gram' => 180,
                'image_url' => 'https://images.unsplash.com/photo-1521369909029-2afed882baee?auto=format&fit=crop&w=800&q=80',
                'variants' => ['ALL SIZE' => 30],
            ],
        ];

        foreach ($products as $product) {
            $variants = $product['variants'];
            unset($product['variants']);

            $model = Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product + ['is_active' => true],
            );

            foreach ($variants as $size => $stock) {
                $model->variants()->updateOrCreate(
                    ['size' => $size],
                    [
                        'sku' => $product['sku'].'-'.str_replace(' ', '-', $size),
                        'stock' => $stock,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
