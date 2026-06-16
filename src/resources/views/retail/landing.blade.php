<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventaris Retail</title>
    <style>
        :root {
            --ink: #17211b;
            --muted: #607064;
            --line: #dfe7df;
            --paper: #fbfcf8;
            --panel: #ffffff;
            --brand: #0f7b55;
            --brand-dark: #07583c;
            --accent: #f2b84b;
            --danger: #c84736;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            color: var(--ink);
            background: var(--paper);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a { color: inherit; text-decoration: none; }
        button, .button { border: 0; cursor: pointer; font: inherit; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 14px clamp(18px, 4vw, 54px);
            border-bottom: 1px solid rgba(23, 33, 27, .08);
            background: rgba(251, 252, 248, .92);
            backdrop-filter: blur(14px);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand-mark {
            display: grid;
            width: 32px;
            height: 32px;
            place-items: center;
            border-radius: 7px;
            background: var(--brand);
            color: white;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 18px;
            color: var(--muted);
            font-size: 14px;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .button {
            display: inline-flex;
            min-height: 42px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 7px;
            padding: 0 16px;
            background: var(--brand);
            color: white;
            font-weight: 700;
        }

        .button.secondary {
            border: 1px solid var(--line);
            background: white;
            color: var(--ink);
        }

        .hero {
            display: grid;
            min-height: calc(100vh - 68px);
            grid-template-columns: minmax(0, 1fr) minmax(360px, .86fr);
            gap: clamp(28px, 5vw, 68px);
            align-items: center;
            padding: 40px clamp(18px, 4vw, 54px) 36px;
        }

        .hero-copy {
            max-width: 740px;
        }

        .eyebrow {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            border: 1px solid #cfe0d5;
            border-radius: 999px;
            padding: 7px 12px;
            color: var(--brand-dark);
            background: #f3faf5;
            font-size: 13px;
            font-weight: 700;
        }

        h1 {
            margin: 18px 0 16px;
            max-width: 680px;
            font-size: clamp(44px, 7vw, 86px);
            line-height: .95;
            letter-spacing: 0;
        }

        .lead {
            max-width: 610px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.65;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .hero-visual {
            position: relative;
            min-height: 540px;
            overflow: hidden;
            border-radius: 8px;
            background:
                linear-gradient(180deg, rgba(6, 36, 24, .05), rgba(6, 36, 24, .26)),
                url("https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=80") center/cover;
        }

        .inventory-strip {
            position: absolute;
            right: 18px;
            bottom: 18px;
            left: 18px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .metric {
            min-height: 88px;
            border: 1px solid rgba(255, 255, 255, .45);
            border-radius: 8px;
            padding: 14px;
            background: rgba(255, 255, 255, .9);
        }

        .metric strong {
            display: block;
            font-size: 24px;
        }

        .metric span {
            color: var(--muted);
            font-size: 13px;
        }

        .mode-badge {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            border: 1px solid #cfe0d5;
            border-radius: 999px;
            padding: 0 14px;
            background: #f3faf5;
            color: var(--brand-dark);
            font-size: 13px;
            font-weight: 800;
        }

        .section {
            padding: 54px clamp(18px, 4vw, 54px);
            border-top: 1px solid var(--line);
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 24px;
        }

        h2 {
            margin: 0;
            font-size: clamp(26px, 4vw, 42px);
            line-height: 1.05;
        }

        .section-head p {
            max-width: 520px;
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .product {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
        }

        .product img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }

        .product-body {
            padding: 14px;
        }

        .product-row {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
        }

        .product h3 {
            margin: 0 0 6px;
            font-size: 16px;
        }

        .product p {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
        }

        .price {
            color: var(--brand-dark);
            font-weight: 800;
            white-space: nowrap;
        }

        .mini-button {
            width: 100%;
            min-height: 38px;
            margin-top: 14px;
            border: 1px solid #cfe0d5;
            border-radius: 7px;
            background: #f6fbf7;
            color: var(--brand-dark);
            font-weight: 800;
        }

        .status {
            margin: 18px clamp(18px, 4vw, 54px) 0;
            border: 1px solid #efd79d;
            border-radius: 8px;
            padding: 12px 14px;
            background: #fff8e4;
            color: #6f5214;
            font-size: 14px;
        }

        .status.error {
            border-color: #f2c3bd;
            background: #fff7f6;
            color: var(--danger);
        }

        .inline-form {
            display: inline-flex;
            margin: 0;
        }

        footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 26px clamp(18px, 4vw, 54px);
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 920px) {
            .nav { display: none; }
            .hero { grid-template-columns: 1fr; }
            .hero-visual { min-height: 420px; }
            .products { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 560px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .actions { width: 100%; }
            .actions .button { flex: 1; }
            .inventory-strip { grid-template-columns: 1fr; }
            .products { grid-template-columns: 1fr; }
            .section-head { align-items: start; flex-direction: column; }
            footer { align-items: start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">IR</span>
            <span>Inventaris Retail</span>
        </a>
        <nav class="nav" aria-label="Navigasi utama">
            <a href="#produk">Produk</a>
        </nav>
        <div class="actions">
            @if (session('admin_authenticated'))
                <span class="mode-badge">Sedang masuk sebagai Admin</span>
                <a class="button" href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                <form class="inline-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Keluar</button>
                </form>
            @elseif (Auth::check())
                <span>Halo, {{ Auth::user()->name }}</span>
                <form class="inline-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Keluar</button>
                </form>
            @else
                <a class="button secondary" href="{{ route('login') }}">Masuk</a>
            @endif
        </div>
    </header>

    <main>
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="status error">{{ $errors->first() }}</div>
        @endif

        <section class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Belanja kebutuhan retail harian</span>
                <h1>Inventaris Retail</h1>
                <p class="lead">
                    Temukan produk kebutuhan harian, pilih barang dari etalase, lalu login dengan Google saat ingin membeli atau checkout.
                </p>
                <div class="hero-actions">
                    <a class="button" href="#produk">Lihat Etalase</a>
                </div>
            </div>
            <div class="hero-visual" aria-label="Rak produk retail">
                <div class="inventory-strip">
                    <div class="metric">
                        <strong>4</strong>
                        <span>produk pilihan</span>
                    </div>
                    <div class="metric">
                        <strong>Google</strong>
                        <span>login pelanggan</span>
                    </div>
                    <div class="metric">
                        <strong>Cepat</strong>
                        <span>alur checkout</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="produk">
            <div class="section-head">
                <h2>Etalase Produk</h2>
                <p>Pilih produk yang ingin dibeli. Jika belum login, sistem akan meminta kamu masuk dengan Google terlebih dahulu sebelum checkout.</p>
            </div>
            <div class="products">
                @forelse ($products as $product)
                    <article class="product">
                        <img src="{{ $product->image_url ?: 'https://placehold.co/800x600/eaf5ed/07583c?text=Produk' }}" alt="{{ $product->name }}">
                        <div class="product-body">
                            <div class="product-row">
                                <div>
                                    <h3>{{ $product->name }}</h3>
                                    <p>{{ $product->description ?: $product->category ?: 'Produk retail tersedia untuk checkout.' }}</p>
                                </div>
                                <span class="price">{{ $product->formatted_price }}</span>
                            </div>
                            <form method="POST" action="{{ route('checkout.store') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button class="mini-button" type="submit" @disabled($product->stock <= 0)>
                                    {{ $product->stock > 0 ? 'Beli / Checkout' : 'Stok Habis' }}
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <article class="product">
                        <div class="product-body">
                            <h3>Belum ada produk</h3>
                            <p>Produk yang ditambahkan admin akan tampil di sini.</p>
                        </div>
                    </article>
                @endforelse
            </div>
        </section>

    </main>

    <footer>
        <span>Inventaris Retail</span>
        <span>Etalase produk retail untuk pelanggan.</span>
    </footer>
</body>
</html>
