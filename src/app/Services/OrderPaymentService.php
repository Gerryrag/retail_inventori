<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Models\AdminNotification;
use RuntimeException;

class OrderPaymentService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function markAsPaid(Order $order, array $payload = []): Order
    {
        return DB::transaction(function () use ($order, $payload): Order {
            $lockedOrder = Order::query()
                ->with(['items', 'paymentTransactions'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->payment_status === 'paid') {
                return $lockedOrder;
            }

            foreach ($lockedOrder->items as $item) {
                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->findOrFail($item->product_variant_id);

                if ($variant->stock < $item->quantity) {
                    throw new RuntimeException("Stok {$item->product_name} ukuran {$item->variant_size} tidak cukup.");
                }

                $stockBefore = $variant->stock;
                $variant->decrement('stock', $item->quantity);
                $variant->refresh();

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $variant->id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $variant->stock,
                    'note' => 'Pemotongan otomatis order '.$lockedOrder->order_number,
                ]);
            }

            $paidAt = now();

            $lockedOrder->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'fulfillment_status' => 'ready_to_ship',
                'paid_at' => $paidAt,
            ]);

            // Create admin notification for payment success
            AdminNotification::create([
                'title' => 'Pembayaran Berhasil: ' . $lockedOrder->order_number,
                'message' => 'Pembayaran untuk order ' . $lockedOrder->order_number . ' dari ' . $lockedOrder->customer_name . ' senilai Rp ' . number_format($lockedOrder->grand_total, 0, ',', '.') . ' telah berhasil diterima.',
                'type' => 'success',
                'is_read' => false,
            ]);

            $lockedOrder->paymentTransactions()->latest()->first()?->update([
                'status' => 'paid',
                'webhook_payload' => $payload,
                'paid_at' => $paidAt,
            ]);

            return $lockedOrder->refresh();
        });
    }
}
