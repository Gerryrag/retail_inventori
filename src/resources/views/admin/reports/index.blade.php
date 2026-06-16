@extends('admin.layout')

@section('title', 'Cetak Laporan')
@section('subtitle', 'Laporan stok, penjualan, dan mutasi dari database.')
@section('actions')
    <button class="button" type="button" onclick="window.print()">Cetak</button>
@endsection

@section('content')
    <section class="stats">
        <article class="stat"><span>Nilai Inventaris</span><strong>Rp{{ number_format($inventoryValue, 0, ',', '.') }}</strong><small>Harga x stok</small></article>
        <article class="stat"><span>Total Penjualan</span><strong>Rp{{ number_format($salesValue, 0, ',', '.') }}</strong><small>Order checkout</small></article>
        <article class="stat"><span>Total Produk</span><strong>{{ $products->count() }}</strong><small>Produk database</small></article>
        <article class="stat"><span>Total Mutasi</span><strong>{{ $movements->count() }}</strong><small>Riwayat stok</small></article>
    </section>
    <section class="panel">
        <div class="panel-head"><div><h2>Laporan Stok</h2><span>Data produk saat ini.</span></div></div>
        <table><thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Nilai</th></tr></thead><tbody>
            @foreach ($products as $product)<tr><td>{{ $product->name }}</td><td>{{ $product->category }}</td><td>{{ $product->formatted_price }}</td><td>{{ $product->stock }}</td><td>Rp{{ number_format($product->price * $product->stock, 0, ',', '.') }}</td></tr>@endforeach
        </tbody></table>
    </section>
    <section class="panel">
        <div class="panel-head"><div><h2>Laporan Pesanan</h2><span>Checkout user.</span></div></div>
        <table><thead><tr><th>Tanggal</th><th>User</th><th>Produk</th><th>Total</th><th>Status</th></tr></thead><tbody>
            @forelse ($orders as $order)<tr><td>{{ $order->created_at->format('d M Y H:i') }}</td><td>{{ $order->user?->name }}</td><td>{{ $order->product_name }}</td><td>{{ $order->formatted_total }}</td><td>{{ $order->status }}</td></tr>@empty<tr><td colspan="5">Belum ada pesanan.</td></tr>@endforelse
        </tbody></table>
    </section>
@endsection
