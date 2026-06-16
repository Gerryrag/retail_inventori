<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Inventaris Retail</title>
    <style>
        :root {
            --ink: #17211b;
            --muted: #607064;
            --line: #dfe7df;
            --paper: #fbfcf8;
            --brand: #0f7b55;
            --brand-dark: #07583c;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            color: var(--ink);
            background: var(--paper);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a { color: inherit; text-decoration: none; }

        .card {
            width: min(100%, 520px);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 28px;
            background: white;
            box-shadow: 0 18px 60px rgba(23, 33, 27, .08);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            font-weight: 800;
        }

        .brand span:first-child {
            display: grid;
            width: 32px;
            height: 32px;
            place-items: center;
            border-radius: 7px;
            background: var(--brand);
            color: white;
        }

        h1 { margin: 0 0 10px; }
        p { margin: 0 0 18px; color: var(--muted); line-height: 1.55; }

        .status {
            margin-bottom: 16px;
            border: 1px solid #cfe0d5;
            border-radius: 8px;
            padding: 12px;
            background: #f3faf5;
            color: var(--brand-dark);
            font-size: 14px;
            font-weight: 700;
        }

        .summary {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 16px;
            background: #f8fbf8;
        }

        .summary strong, .summary span {
            display: block;
        }

        .summary span {
            margin-top: 6px;
            color: var(--brand-dark);
            font-weight: 800;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 22px;
        }

        .button {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            padding: 0 16px;
            background: var(--brand);
            color: white;
            font-weight: 800;
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: white;
            color: var(--ink);
        }
    </style>
</head>
<body>
    <main class="card">
        <a class="brand" href="{{ route('home') }}">
            <span>IR</span>
            <span>Inventaris Retail</span>
        </a>

        <h1>Checkout</h1>
        <p>Halo, {{ Auth::user()->name }}. Kamu sudah login dengan Google dan bisa melanjutkan pembelian.</p>

        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <div class="summary">
            @if ($order)
                <strong>{{ $order->product_name }}</strong>
                <span>{{ $order->formatted_total }} · Status: {{ ucfirst($order->status) }}</span>
            @else
                <strong>Belum ada produk dipilih</strong>
                <span>Pilih produk dari etalase terlebih dahulu.</span>
            @endif
        </div>

        <div class="actions">
            <a class="button secondary" href="{{ route('home') }}#produk">Kembali Belanja</a>
            <a class="button" href="{{ route('home') }}">Selesai Preview</a>
        </div>
    </main>
</body>
</html>
