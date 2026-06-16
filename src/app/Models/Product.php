<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'category', 'price', 'stock', 'image_url', 'is_active'])]
class Product extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp'.number_format($this->price, 0, ',', '.');
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Habis';
        }

        if ($this->stock <= 10) {
            return 'Kritis';
        }

        if ($this->stock <= 30) {
            return 'Menipis';
        }

        return 'Aman';
    }
}
