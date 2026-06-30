<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DokuPaymentService
{
    private const CHECKOUT_TARGET = '/checkout/v1/payment';

    /**
     * @return array<string, mixed>
     */
    public function createCheckoutPayment(Order $order): array
    {
        $clientId = (string) config('services.doku.client_id');
        $secretKey = (string) config('services.doku.secret_key');

        if ($clientId === '' || $secretKey === '') {
            throw new RuntimeException('DOKU_CLIENT_ID dan DOKU_SECRET_KEY belum diisi.');
        }

        $order->loadMissing(['items.product', 'items.variant.product']);

        $payload = $this->payload($order);
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);

        if ($body === false) {
            throw new RuntimeException('Gagal membuat payload JSON DOKU.');
        }

        $requestId = (string) Str::uuid();
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');
        $digest = $this->digest($body);

        $response = Http::withHeaders([
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Request-Target' => self::CHECKOUT_TARGET,
            'Digest' => 'SHA-256='.$digest,
            'Signature' => $this->signature($clientId, $requestId, $timestamp, self::CHECKOUT_TARGET, $digest, $secretKey),
            'Content-Type' => 'application/json',
        ])
            ->acceptJson()
            ->withBody($body, 'application/json')
            ->post($this->baseUrl().self::CHECKOUT_TARGET);

        $responseData = $response->json() ?? [];

        $order->paymentTransactions()->latest()->first()?->update([
            'request_payload' => $payload,
            'response_payload' => $responseData,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('DOKU menolak request: '.$response->body());
        }

        $paymentUrl = data_get($responseData, 'response.payment.url');

        if (! is_string($paymentUrl) || $paymentUrl === '') {
            throw new RuntimeException('Response DOKU tidak memiliki response.payment.url.');
        }

        $order->update(['payment_url' => $paymentUrl]);
        $order->paymentTransactions()->latest()->first()?->update([
            'payment_url' => $paymentUrl,
            'status' => 'pending',
        ]);

        return $responseData;
    }

    public function validIncomingSignature(string $rawBody, string $target, string $clientId, string $requestId, string $timestamp, string $signature): bool
    {
        $secretKey = (string) config('services.doku.secret_key');

        if ($secretKey === '') {
            return false;
        }

        $expected = $this->signature($clientId, $requestId, $timestamp, $target, $this->digest($rawBody), $secretKey);

        return hash_equals($expected, $signature);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Order $order): array
    {
        return [
            'order' => [
                'amount' => $order->grand_total,
                'invoice_number' => $order->doku_invoice_number,
                'currency' => 'IDR',
                'callback_url' => route('admin.orders.index'),
                'callback_url_result' => route('admin.orders.index'),
                'auto_redirect' => true,
                'line_items' => $this->buildLineItems($order),
            ],
            'payment' => [
                'payment_due_date' => (int) config('services.doku.payment_due_minutes', 60),
            ],
            'customer' => [
                'name' => $this->sanitize((string) $order->customer_name),
                'email' => $order->customer_email,
                'phone' => $this->sanitize((string) $order->customer_phone),
                'address' => $this->sanitize((string) $order->shipping_address),
                'country' => 'ID',
            ],
            'shipping_address' => [
                'first_name' => $this->sanitize((string) $order->customer_name),
                'address' => $this->sanitize((string) $order->shipping_address),
                'city' => $this->sanitize((string) $order->destination_city_name),
                'phone' => $this->sanitize((string) $order->customer_phone),
                'country_code' => 'ID',
            ],
            'additional_info' => [
                'override_notification_url' => (string) config('services.doku.notification_url'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildLineItems(Order $order): array
    {
        $lineItems = $order->items->map(fn ($item): array => [
            'id' => (string) $item->product_variant_id,
            'name' => $this->sanitize($item->product_name.' '.$item->variant_size),
            'price' => $item->unit_price,
            'quantity' => $item->quantity,
            'sku' => (string) $item->variant?->sku,
            'category' => (string) $item->product?->category,
            'image_url' => (string) $item->product?->image_url,
        ])->values()->all();

        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'id' => 'shipping',
                'name' => $this->sanitize('Shipping Cost ' . strtoupper((string) $order->courier)),
                'price' => $order->shipping_cost,
                'quantity' => 1,
            ];
        }

        return $lineItems;
    }

    private function sanitize(string $value): string
    {
        // Allowed characters: a-z A-Z 0-9 . - / + , = _ : ' @ % and space
        return preg_replace('/[^a-zA-Z0-9.\-\/+,=_:\'@% ]/', '', $value);
    }

    private function signature(string $clientId, string $requestId, string $timestamp, string $target, string $digest, string $secretKey): string
    {
        $component = "Client-Id:{$clientId}\n"
            ."Request-Id:{$requestId}\n"
            ."Request-Timestamp:{$timestamp}\n"
            ."Request-Target:{$target}\n"
            ."Digest:{$digest}";

        return 'HMACSHA256='.base64_encode(hash_hmac('sha256', $component, $secretKey, true));
    }

    private function digest(string $body): string
    {
        return base64_encode(hash('sha256', $body, true));
    }

    private function baseUrl(): string
    {
        return config('services.doku.env') === 'production'
            ? (string) config('services.doku.production_base_url')
            : (string) config('services.doku.sandbox_base_url');
    }
}
