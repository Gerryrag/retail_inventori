<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Official Merchandise</title>
    <style>
        :root {
            --ink: #17211b;
            --muted: #607064;
            --line: #dfe7df;
            --paper: #fbfcf8;
            --brand: #0f7b55;
            --brand-dark: #07583c;
            --warn: #f2b84b;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background:
                linear-gradient(90deg, rgba(251, 252, 248, .94), rgba(251, 252, 248, .72)),
                url("https://images.unsplash.com/photo-1604719312566-8912e9227c6a?auto=format&fit=crop&w=1600&q=80") center/cover;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            display: grid;
            min-height: 100vh;
            grid-template-columns: minmax(0, 1fr) 420px;
            align-items: stretch;
        }

        .intro {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 34px clamp(22px, 5vw, 64px);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            font-weight: 800;
        }

        .mark {
            display: grid;
            width: 34px;
            height: 34px;
            place-items: center;
            border-radius: 7px;
            background: var(--brand);
            color: white;
        }

        .intro h1 {
            max-width: 680px;
            margin: 0 0 16px;
            font-size: clamp(38px, 6vw, 72px);
            line-height: .98;
            letter-spacing: 0;
        }

        .intro p {
            max-width: 540px;
            margin: 0;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.6;
        }

        .panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 20px;
            border-left: 1px solid rgba(23, 33, 27, .12);
            background: rgba(255, 255, 255, .92);
            padding: 34px;
            backdrop-filter: blur(12px);
        }

        .panel h2 {
            margin: 0;
            font-size: 28px;
        }

        .panel > p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
        }

        .status {
            border: 1px solid #efd79d;
            border-radius: 8px;
            padding: 12px;
            background: #fff8e4;
            color: #6f5214;
            font-size: 14px;
        }

        .google {
            display: inline-flex;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: white;
            font-weight: 800;
        }

        .google span {
            display: grid;
            width: 24px;
            height: 24px;
            place-items: center;
            border-radius: 50%;
            color: #4285f4;
            font-weight: 900;
        }

        .muted-link {
            color: var(--brand-dark);
            font-weight: 800;
        }

        @media (max-width: 860px) {
            .shell { grid-template-columns: 1fr; }
            .panel { border-left: 0; border-top: 1px solid rgba(23, 33, 27, .12); }
            .intro { min-height: 420px; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="intro">
            <a class="brand" href="{{ route('login') }}">
                <span class="mark">IR</span>
                <span>Official Merchandise</span>
            </a>
            <div>
                <h1>Akses admin lewat Google.</h1>
                <p>Gunakan akun Google yang sudah didaftarkan sebagai admin untuk mengelola produk, stok, laporan, notifikasi, dan chat.</p>
            </div>
        </section>

        <section class="panel">
            <h2>Masuk Admin</h2>
            <p>Username dan password manual sudah dinonaktifkan. Sistem hanya menerima email Google yang ada di daftar admin.</p>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <a class="google" href="{{ route('google.redirect') }}">
                <span>G</span>
                Masuk dengan Google
            </a>
        </section>
    </main>
</body>
</html>
