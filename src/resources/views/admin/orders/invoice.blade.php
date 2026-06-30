<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { margin: 0; padding: 32px; color: #15211b; font-family: Arial, sans-serif; }
        .top { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #15211b; padding-bottom: 18px; }
        h1, h2, p { margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { border-bottom: 1px solid #dce6df; padding: 10px; text-align: left; }
        .right { text-align: right; }
        .actions { margin-bottom: 18px; }
        button { min-height: 38px; padding: 0 14px; border: 0; background: #0f7b55; color: white; font-weight: 700; }
        @media print { .actions { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">Cetak / Save PDF</button></div>
    <section class="top">
        <div>
            <h1>Official Merchandise</h1>
            <p>Invoice Penjualan</p>
        </div>
        <div class="right">
            <h2>{{ $order->order_number }}</h2>
            <p>{{ $order->created_at->format('d M Y H:i') }}</p>
            <p>Status: {{ $order->payment_status }}</p>
        </div>
    </section>
    <table>
        <tr><th>Customer</th><td>{{ $order->customer_name }} · {{ $order->customer_phone }}</td></tr>
        <tr><th>Alamat</th><td>{{ $order->shipping_address }}</td></tr>
        <tr><th>Payment</th><td>{{ $order->doku_invoice_number }}</td></tr>
    </table>
    <table>
        <thead><tr><th>Produk</th><th>Ukuran</th><th>Qty</th><th>Harga</th><th>Total</th></tr></thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr><td>{{ $item->product_name }}</td><td>{{ $item->variant_size }}</td><td>{{ $item->quantity }}</td><td>Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td><td>Rp{{ number_format($item->total_price, 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><th colspan="4" class="right">Subtotal</th><th>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</th></tr>
            <tr><th colspan="4" class="right">Ongkir</th><th>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</th></tr>
            <tr><th colspan="4" class="right">Grand Total</th><th>{{ $order->formatted_grand_total }}</th></tr>
        </tfoot>
    </table>
</body>
</html>
