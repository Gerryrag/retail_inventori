@extends('admin.layout')

@section('title', 'Dashboard')
@section('subtitle', 'Real-time snapshot for merchandise revenue, orders, chat, and stock risk.')

@section('content')
    <section class="stats">
        <article class="stat">
            <div class="stat-header">
                <span>Total Revenue</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
            </div>
            <strong>Rp{{ number_format($monthlyRevenue, 0, ',', '.') }}</strong>
            <small>{{ $paidOrdersThisMonth }} paid orders this month</small>
        </article>
        <article class="stat">
            <div class="stat-header">
                <span>Total Orders</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
            <strong>{{ $totalOrders }}</strong>
            <small>All orders in database</small>
        </article>
        <article class="stat">
            <div class="stat-header">
                <span>Active Chats</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <strong>{{ $activeChats }}</strong>
            <small>{{ $totalMessages }} stored messages</small>
        </article>
        <article class="stat">
            <div class="stat-header">
                <span>Low Stock Alerts</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>
            </div>
            <strong>{{ $lowStockVariants }}</strong>
            <small>
                @forelse ($lowStockList as $variant)
                    {{ $variant->product?->name }} ({{ $variant->size }}) - {{ $variant->stock }} left<br>
                @empty
                    All variants are healthy
                @endforelse
            </small>
        </article>
    </section>

    <section class="grid">
        <div class="panel">
            <div class="panel-head">
                <div><h2>Monthly Sales Performance</h2><span>Revenue from paid orders across this year.</span></div>
                <span class="badge paid">IDR</span>
            </div>
            <div class="chart">
                @foreach ($salesChart as $point)
                    <div class="chart-bar" title="{{ $point['label'] }}: Rp{{ number_format($point['value'], 0, ',', '.') }}">
                        <i style="height: {{ max(12, round(($point['value'] / $maxSales) * 180)) }}px"></i>
                        <span>{{ $point['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Top Variants</h2><span>Best performing sizes from paid orders.</span></div></div>
            <div class="list">
                @forelse ($topVariants as $variant)
                    <article class="notice"><strong>{{ $variant->product_name }} · Size {{ $variant->variant_size }}</strong><p>{{ $variant->sold_qty }} pcs sold · Rp{{ number_format($variant->revenue, 0, ',', '.') }}</p></article>
                @empty
                    <article class="notice"><strong>No paid orders yet</strong><p>Sales performance appears after a DOKU payment is marked paid.</p></article>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Inventory Overview</h2><span>Variant stock distribution by product.</span></div></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Product</th><th>Price</th><th>Total Stock</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($products->take(8) as $product)
                            <tr>
                                <td><strong>{{ $product->name }}</strong><br><span>{{ $product->variants->map(fn ($variant) => $variant->size.': '.$variant->stock)->join(' · ') }}</span></td>
                                <td>{{ $product->formatted_price }}</td>
                                <td>{{ $product->total_stock }}</td>
                                <td><span class="badge {{ $product->total_stock <= 5 ? 'danger' : 'success' }}">{{ $product->stock_status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No products yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Recent Orders</h2><span>Payment and fulfillment health.</span></div></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr><td>{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->formatted_grand_total }}</td><td><span class="badge {{ $order->payment_status === 'paid' ? 'paid' : 'pending' }}">{{ $order->payment_status }}</span></td></tr>
                        @empty
                            <tr><td colspan="4">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
