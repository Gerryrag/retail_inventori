@extends('admin.layout')

@section('title', 'Dashboard Admin')
@section('subtitle', 'Ringkasan data produk, stok, pesanan, notifikasi, dan chat.')
@section('actions')
    <a class="button" href="{{ route('admin.products.index') }}">Kelola Barang</a>
@endsection

@section('content')
    <section class="stats">
        <article class="stat"><span>Total Produk</span><strong>{{ $totalProducts }}</strong><small>Produk tersimpan</small></article>
        <article class="stat"><span>Total Stok</span><strong>{{ $totalStock }}</strong><small>Unit tersedia</small></article>
        <article class="stat"><span>Nilai Inventaris</span><strong>Rp{{ number_format($totalInventoryValue, 0, ',', '.') }}</strong><small>Harga jual x stok</small></article>
        <article class="stat"><span>Stok Kritis</span><strong>{{ $lowStockProducts }}</strong><small>Stok ≤ 10</small></article>
        <article class="stat"><span>Total Pesanan</span><strong>{{ $totalOrders }}</strong><small>Checkout user</small></article>
        <article class="stat"><span>Pencatatan</span><strong>{{ $totalMovements }}</strong><small>Mutasi stok</small></article>
        <article class="stat"><span>Notifikasi Baru</span><strong>{{ $unreadNotifications }}</strong><small>Belum dibaca</small></article>
        <article class="stat"><span>Pesan Chat</span><strong>{{ $totalMessages }}</strong><small>Riwayat pesan</small></article>
    </section>

    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Produk Terbaru</h2><span>Data dari tabel products.</span></div></div>
            <table>
                <thead><tr><th>Produk</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($products->take(8) as $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong><br><span>{{ $product->category ?: 'Tanpa kategori' }}</span></td>
                            <td>{{ $product->formatted_price }}</td>
                            <td>{{ $product->stock }}</td>
                            <td><span class="badge">{{ $product->stock_status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Pesanan Terbaru</h2><span>Data dari tabel orders.</span></div></div>
            <div class="list">
                @forelse ($orders as $order)
                    <article class="notice"><strong>{{ $order->product_name }} · {{ $order->formatted_total }}</strong><p>{{ $order->user?->name ?? 'User dihapus' }} · {{ $order->created_at->format('d M Y H:i') }}</p></article>
                @empty
                    <article class="notice"><strong>Belum ada pesanan</strong><p>Pesanan muncul setelah user checkout.</p></article>
                @endforelse
            </div>
        </div>
    </section>
@endsection
