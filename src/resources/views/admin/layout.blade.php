<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') - Inventaris Retail</title>
    <style>
        :root { --ink:#15211b; --muted:#627369; --line:#dce6df; --paper:#f7faf6; --panel:#fff; --brand:#0f7b55; --brand-dark:#07583c; --danger:#c84736; }
        * { box-sizing: border-box; }
        body { margin:0; color:var(--ink); background:var(--paper); font-family:Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        a { color:inherit; text-decoration:none; }
        button, input, select, textarea { font:inherit; }
        form { margin:0; }
        .layout { display:grid; min-height:100vh; grid-template-columns:260px minmax(0, 1fr); }
        aside { position:sticky; top:0; height:100vh; border-right:1px solid var(--line); background:#fff; padding:18px; }
        .brand { display:flex; align-items:center; gap:10px; margin-bottom:28px; font-weight:900; }
        .brand span:first-child { display:grid; width:34px; height:34px; place-items:center; border-radius:7px; background:var(--brand); color:white; }
        .menu { display:grid; gap:6px; }
        .menu a { display:flex; align-items:center; min-height:42px; border-radius:7px; padding:0 12px; color:var(--muted); font-weight:700; }
        .menu a.active, .menu a:hover { background:#eaf5ed; color:var(--brand-dark); }
        .sidebar-foot { position:absolute; right:18px; bottom:18px; left:18px; border:1px solid var(--line); border-radius:8px; padding:12px; color:var(--muted); font-size:13px; line-height:1.45; }
        main { min-width:0; }
        .topbar { position:sticky; top:0; z-index:5; display:flex; align-items:center; justify-content:space-between; gap:18px; padding:18px 24px; border-bottom:1px solid var(--line); background:rgba(247,250,246,.9); backdrop-filter:blur(14px); }
        .topbar h1 { margin:0; font-size:24px; }
        .topbar p { margin:4px 0 0; color:var(--muted); font-size:14px; }
        .top-actions { display:flex; align-items:center; gap:10px; }
        .content { display:grid; gap:18px; padding:22px 24px 34px; }
        .button, .secondary-button, .danger-button { display:inline-grid; min-height:40px; place-items:center; border:1px solid var(--brand); border-radius:7px; padding:0 14px; background:var(--brand); color:white; cursor:pointer; font-weight:800; }
        .secondary-button { border-color:var(--line); background:white; color:var(--ink); }
        .danger-button { border-color:var(--danger); background:var(--danger); }
        .status, .errors { border:1px solid #cfe0d5; border-radius:8px; padding:12px 14px; background:#f3faf5; color:var(--brand-dark); font-size:14px; font-weight:700; }
        .errors { border-color:#f2c3bd; background:#fff7f6; color:var(--danger); }
        .stats { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:14px; }
        .stat, .panel { border:1px solid var(--line); border-radius:8px; background:var(--panel); }
        .stat { min-height:116px; padding:16px; }
        .stat span { color:var(--muted); font-size:13px; font-weight:700; }
        .stat strong { display:block; margin-top:10px; font-size:30px; }
        .stat small { display:block; margin-top:8px; color:var(--brand-dark); font-weight:700; }
        .grid { display:grid; grid-template-columns:minmax(0, 1.2fr) minmax(360px, .8fr); gap:18px; align-items:start; }
        .panel-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px; border-bottom:1px solid var(--line); }
        .panel-head h2 { margin:0; font-size:18px; }
        .panel-head span { color:var(--muted); font-size:13px; }
        .form-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; padding:16px; }
        label { display:grid; gap:7px; color:var(--muted); font-size:13px; font-weight:800; }
        input, select, textarea { width:100%; min-height:42px; border:1px solid var(--line); border-radius:7px; padding:0 11px; background:white; color:var(--ink); }
        textarea { min-height:84px; padding-top:10px; resize:vertical; }
        .full { grid-column:1 / -1; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:14px 16px; border-bottom:1px solid var(--line); text-align:left; vertical-align:middle; font-size:14px; }
        th { color:var(--muted); font-size:12px; text-transform:uppercase; }
        .thumb { width:64px; height:48px; border:1px solid var(--line); border-radius:7px; object-fit:cover; background:#eef6ef; }
        .badge { display:inline-flex; min-height:26px; align-items:center; border-radius:999px; padding:0 9px; background:#eef6ef; color:var(--brand-dark); font-size:12px; font-weight:800; }
        .badge.warn { background:#fff4d8; color:#8a5a05; }
        .badge.danger { background:#fdecea; color:var(--danger); }
        .list { display:grid; gap:12px; padding:16px; }
        .notice { display:grid; gap:5px; border:1px solid var(--line); border-radius:8px; padding:12px; background:#fbfdfb; }
        .notice p { margin:0; color:var(--muted); font-size:13px; line-height:1.45; }
        .inline-actions { display:flex; flex-wrap:wrap; gap:8px; }
        @media (max-width:1080px) { .layout{grid-template-columns:1fr} aside{position:relative;height:auto}.sidebar-foot{position:static;margin-top:18px}.grid{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,minmax(0,1fr))} }
        @media (max-width:680px) { .topbar{align-items:start;flex-direction:column}.top-actions{width:100%;flex-wrap:wrap}.button,.secondary-button,.danger-button{flex:1}.stats,.form-grid{grid-template-columns:1fr} }
    </style>
</head>
<body>
    <div class="layout">
        <aside>
            <a class="brand" href="{{ route('admin.dashboard') }}"><span>IR</span><span>Admin Retail</span></a>
            <nav class="menu" aria-label="Menu admin">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Daftar Barang</a>
                <a class="{{ request()->routeIs('admin.stock.*') ? 'active' : '' }}" href="{{ route('admin.stock.index') }}">Pencatatan</a>
                <a class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">Cetak Laporan</a>
                <a class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">Notifikasi</a>
                <a class="{{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">Chat</a>
                <a href="{{ route('home') }}">Etalase User</a>
            </nav>
            <div class="sidebar-foot">Database aktif memakai Neon.tech. Gambar produk memakai URL Cloudinary dan tidak diupload ke server app.</div>
        </aside>
        <main>
            <header class="topbar">
                <div>
                    <h1>@yield('title', 'Dashboard Admin')</h1>
                    <p>@yield('subtitle', 'Kelola data retail dari database.')</p>
                </div>
                <div class="top-actions">
                    @yield('actions')
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="secondary-button" type="submit">Keluar</button></form>
                </div>
            </header>
            <div class="content">
                @if (session('status')) <div class="status">{{ session('status') }}</div> @endif
                @if ($errors->any()) <div class="errors">{{ $errors->first() }}</div> @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
