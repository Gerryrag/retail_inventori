<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'tracking_number', 'courier', 'status', 'description', 'raw_response', 'tracked_at'])]
class OrderTracking extends Model
{
    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
            'tracked_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
