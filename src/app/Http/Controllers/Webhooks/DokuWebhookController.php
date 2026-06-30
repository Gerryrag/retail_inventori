<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DokuPaymentService;
use App\Services\OrderPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class DokuWebhookController extends Controller
{
    public function __invoke(Request $request, OrderPaymentService $payments, DokuPaymentService $doku): JsonResponse
    {
        if ((bool) config('services.doku.validate_signature', true)) {
            $clientId = (string) $request->header('Client-Id');
            $requestId = (string) $request->header('Request-Id');
            $timestamp = (string) $request->header('Request-Timestamp');
            $signature = (string) $request->header('Signature');

            if (
                $clientId === ''
                || $requestId === ''
                || $timestamp === ''
                || $signature === ''
                || ! $doku->validIncomingSignature($request->getContent(), $request->getPathInfo(), $clientId, $requestId, $timestamp, $signature)
            ) {
                return response()->json(['message' => 'Invalid DOKU signature.'], 401);
            }
        }

        $payload = $request->all();
        $invoiceNumber = $request->input('invoice_number')
            ?? $request->input('order.invoice_number')
            ?? $request->input('doku_invoice_number');

        $status = strtolower((string) (
            $request->input('status')
            ?? $request->input('transaction.status')
            ?? $request->input('payment.status')
        ));

        if (! $invoiceNumber) {
            return response()->json(['message' => 'invoice_number wajib diisi'], 422);
        }

        $order = Order::query()
            ->where('doku_invoice_number', $invoiceNumber)
            ->orWhereHas('paymentTransactions', fn ($query) => $query->where('invoice_number', $invoiceNumber))
            ->firstOrFail();

        if (! in_array($status, ['paid', 'success', 'settlement', 'capture'], true)) {
            $order->paymentTransactions()->latest()->first()?->update([
                'status' => $status ?: 'pending',
                'webhook_payload' => $payload,
            ]);

            return response()->json(['message' => 'Webhook diterima tanpa pemotongan stok.', 'status' => $status ?: 'pending']);
        }

        try {
            $payments->markAsPaid($order, $payload);
        } catch (RuntimeException $exception) {
            $order->update(['status' => 'stock_problem']);

            return response()->json(['message' => $exception->getMessage()], 409);
        }

        return response()->json(['message' => 'Order paid dan stok varian berhasil dipotong.']);
    }
}
