<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'order_number',
    'customer_name',
    'customer_email',
    'customer_phone',
    'shipping_address',
    'destination_city_id',
    'destination_city_name',
    'status',
    'payment_status',
    'fulfillment_status',
    'subtotal',
    'shipping_cost',
    'grand_total',
    'courier',
    'courier_service',
    'doku_invoice_number',
    'payment_url',
    'paid_at',
])]
class Order extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'grand_total' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(OrderTracking::class);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp'.number_format($this->grand_total, 0, ',', '.');
    }
}
