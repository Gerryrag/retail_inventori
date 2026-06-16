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
                'name' => 'Beras Premium 5kg',
                'description' => 'Stok stabil untuk kebutuhan harian.',
                'category' => 'Sembako',
                'price' => 74000,
                'stock' => 248,
                'image_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Minyak Goreng 2L',
                'description' => 'Produk cepat jalan untuk rumah tangga.',
                'category' => 'Sembako',
                'price' => 37500,
                'stock' => 38,
                'image_url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Kopi Robusta 250g',
                'description' => 'Kategori minuman dengan margin tinggi.',
                'category' => 'Minuman',
                'price' => 28000,
                'stock' => 94,
                'image_url' => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Paket Snack Hemat',
                'description' => 'Bundling retail untuk pembelian cepat.',
                'category' => 'Makanan Ringan',
                'price' => 19900,
                'stock' => 11,
                'image_url' => 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product + ['is_active' => true],
            );
        }
    }
}
