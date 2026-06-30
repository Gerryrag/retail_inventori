<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Packing Slip {{ $order->order_number }}</title>
    <style>
        body { margin: 0; padding: 28px; color: #15211b; font-family: Arial, sans-serif; }
        .label { border: 2px solid #15211b; padding: 18px; }
        h1, h2, p { margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border-bottom: 1px solid #dce6df; padding: 10px; text-align: left; }
        .actions { margin-bottom: 18px; }
        button { min-height: 38px; padding: 0 14px; border: 0; background: #0f7b55; color: white; font-weight: 700; }
        @media print { .actions { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">Cetak / Save PDF</button></div>
    <main class="label">
        <h1>Packing Slip</h1>
        <h2>{{ $order->order_number }}</h2>
        <p><strong>Kepada:</strong> {{ $order->customer_name }} · {{ $order->customer_phone }}</p>
        <p><strong>Alamat:</strong> {{ $order->shipping_address }}</p>
        <p><strong>Kurir:</strong> {{ $order->shipment?->courier ?: $order->courier }} {{ $order->shipment?->service ?: $order->courier_service }}</p>
        <p><strong>Resi:</strong> {{ $order->shipment?->tracking_number ?: '-' }}</p>
        <table>
            <thead><tr><th>Produk</th><th>Ukuran</th><th>Qty</th></tr></thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr><td>{{ $item->product_name }}</td><td>{{ $item->variant_size }}</td><td>{{ $item->quantity }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
