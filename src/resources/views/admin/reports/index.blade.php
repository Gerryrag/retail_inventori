@extends('admin.layout')

@section('title', 'Cetak Laporan')
@section('subtitle', 'Laporan inventory, omset, penjualan, dan mutasi stok.')
@section('actions')
    <button class="button" type="button" onclick="window.print()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Cetak Laporan
    </button>
@endsection

@section('content')
    <section class="stats">
        <article class="stat">
            <div class="stat-header">
                <span>Omset Bulan Ini</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <strong>Rp{{ number_format($monthlyRevenue, 0, ',', '.') }}</strong>
            <small>Order paid</small>
        </article>
        <article class="stat">
            <div class="stat-header">
                <span>Nilai Inventaris</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/></svg>
            </div>
            <strong>Rp{{ number_format($inventoryValue, 0, ',', '.') }}</strong>
            <small>Harga x stok varian</small>
        </article>
        <article class="stat">
            <div class="stat-header">
                <span>Total Produk</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            </div>
            <strong>{{ $products->count() }}</strong>
            <small>Produk database</small>
        </article>
        <article class="stat">
            <div class="stat-header">
                <span>Total Mutasi</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            </div>
            <strong>{{ $movements->count() }}</strong>
            <small>Riwayat stok</small>
        </article>
    </section>
    
    <section class="panel" style="margin-top: 24px;">
        <div class="panel-head"><div><h2>Laporan Stok Varian</h2><span>Data produk dan ukuran saat ini.</span></div></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Produk</th><th>SKU</th><th>Ukuran</th><th>Harga</th><th>Stok</th><th>Nilai</th></tr></thead>
                <tbody>
                    @foreach ($products as $product)
                        @foreach ($product->variants as $variant)
                            <tr><td>{{ $product->name }}</td><td><code style="font-family: monospace; font-size: 12px;">{{ $variant->sku }}</code></td><td>{{ $variant->size }}</td><td>{{ $product->formatted_price }}</td><td>{{ $variant->stock }}</td><td>Rp{{ number_format($product->price * $variant->stock, 0, ',', '.') }}</td></tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    
    <section class="grid" style="margin-top: 24px;">
        <div class="panel">
            <div class="panel-head"><div><h2>Varian Terlaris</h2><span>Berdasarkan order paid.</span></div></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Produk</th><th>Ukuran</th><th>Terjual</th><th>Omset</th></tr></thead>
                    <tbody>
                        @forelse ($topVariants as $variant)
                            <tr><td>{{ $variant->product_name }}</td><td>{{ $variant->variant_size }}</td><td>{{ $variant->sold_qty }} pcs</td><td>Rp{{ number_format($variant->revenue, 0, ',', '.') }}</td></tr>
                        @empty
                            <tr><td colspan="4" style="color: var(--muted); text-align: center; padding: 16px;">Belum ada penjualan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Order Paid</h2><span>Riwayat pembayaran sukses.</span></div></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Tanggal Paid</th></tr></thead>
                    <tbody>
                        @forelse ($paidOrders as $order)
                            <tr><td>{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->formatted_grand_total }}</td><td>{{ $order->paid_at?->format('d M Y H:i') }}</td></tr>
                        @empty
                            <tr><td colspan="4" style="color: var(--muted); text-align: center; padding: 16px;">Belum ada order paid.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
