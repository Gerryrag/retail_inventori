@extends('admin.layout')

@section('title', 'Orders Management')
@section('subtitle', 'Fulfillment view for payments, purchased sizes, invoice, packing slip, and tracking resi.')

@section('actions')
    <button class="button" onclick="openModal('add-order-modal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Test Order
    </button>
@endsection

@section('content')
<!-- Local page styling for overlays and bento layouts -->
<style>
    /* Modal Container & Overlay */
    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 100;
        background: rgba(9, 20, 38, 0.4);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        animation: fadeIn 0.2s ease-out;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    .modal-content {
        background: var(--surface);
        border: 1px solid var(--line-light);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--line-light);
        position: sticky;
        top: 0;
        background: var(--surface);
        z-index: 10;
    }
    
    .modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--charcoal);
    }
    
    .modal-body {
        padding: 24px;
    }

    .form-card {
        background: #ffffff;
        border: 1px solid var(--line-light);
        border-radius: var(--radius);
        padding: 20px;
    }
    
    .form-card-title {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 16px;
        border-bottom: 1px solid var(--line-light);
        padding-bottom: 8px;
    }
    
    .sub-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateY(16px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

@php
    $totalRevenue = $orders->where('payment_status', 'paid')->sum('grand_total');
    $activeOrders = $orders->where('payment_status', 'paid')->whereNotIn('fulfillment_status', ['delivered'])->count();
    $pendingPayments = $orders->where('payment_status', 'pending')->count();
    $totalCount = $orders->count();
@endphp

<!-- Bento Summary Cards -->
<div class="stats" style="margin-bottom: 24px;">
    <div class="stat">
        <div class="stat-header">
            <span>Total Revenue</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </div>
        <strong>Rp{{ number_format($totalRevenue, 0, ',', '.') }}</strong>
        <small>Dari seluruh pesanan lunas</small>
    </div>
    <div class="stat">
        <div class="stat-header">
            <span>Active Orders</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <strong>{{ $activeOrders }}</strong>
        <small>Perlu diproses/dikirim</small>
    </div>
    <div class="stat">
        <div class="stat-header">
            <span>Pending Payments</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
        </div>
        <strong>{{ $pendingPayments }}</strong>
        <small>Menunggu pembayaran DOKU</small>
    </div>
    <div class="stat">
        <div class="stat-header">
            <span>Total Orders</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <strong>{{ $totalCount }}</strong>
        <small>Seluruh transaksi masuk</small>
    </div>
</div>

<!-- Main Table Catalog -->
<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Orders & Tracking</h2>
            <span>Recent orders with DOKU payment state and fulfillment actions.</span>
        </div>
        <span class="badge">{{ $totalCount }} orders</span>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Items Purchased</th>
                    <th>Total Price</th>
                    <th>Payment Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong><br>
                            <span>{{ $order->created_at->format('d M Y H:i') }}</span>
                        </td>
                        <td>
                            <strong>{{ $order->customer_name }}</strong><br>
                            <span>{{ $order->customer_phone ?: $order->customer_email }}</span>
                        </td>
                        <td>
                            @forelse ($order->items as $item)
                                <span>{{ $item->product_name }} ({{ $item->variant_size }} · {{ $item->quantity }} pcs)</span><br>
                            @empty
                                <span class="badge danger">No items</span>
                            @endforelse
                        </td>
                        <td><strong>{{ $order->formatted_grand_total }}</strong></td>
                        <td>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'paid' : 'pending' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="inline-actions" style="justify-content: flex-end; gap: 8px;">
                                <!-- Payment Link -->
                                @if ($order->payment_url)
                                    <a class="icon-action" href="{{ $order->payment_url }}" target="_blank" title="Open DOKU payment">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.orders.doku-payment', $order) }}" style="display: inline;">
                                        @csrf
                                        <button class="icon-action" type="submit" title="Create DOKU payment link">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Document actions -->
                                <a class="icon-action" href="{{ route('admin.orders.invoice', $order) }}" target="_blank" title="Print invoice">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                                </a>
                                <a class="icon-action" href="{{ route('admin.orders.packing-slip', $order) }}" target="_blank" title="Print packing slip">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                </a>
                                
                                <!-- Update Tracking Modal Trigger -->
                                <button class="icon-action" onclick="openModal('tracking-modal-{{ $order->id }}')" title="Update tracking resi">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 32px;">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<!-- Sandbox Integration Info Panel -->
<div class="panel" style="margin-top: 24px;">
    <div class="panel-head"><div><h2>DOKU Sandbox Configurations</h2><span>Credentials and notification setup details.</span></div></div>
    <div class="list">
        <article class="notice">
            <strong>Payment Flow Simulator</strong>
            <p>Ensure that DOKU credentials are correctly filled in the config files. Creating a test order generates a pending transaction which can be tested using the checkout simulation link.</p>
        </article>
        <article class="notice">
            <strong>Notification URL (Webhook callback)</strong>
            <p>Webhook URL endpoint for payment status synchronization: <code>{{ url('/webhooks/doku') }}</code></p>
        </article>
    </div>
</div>

<!-- ==========================================
     MODAL: CREATE TEST ORDER
     ========================================== -->
<div class="modal-overlay" id="add-order-modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3>Create Test Order</h3>
            <button onclick="closeModal('add-order-modal')" style="color: var(--muted);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.orders.store') }}">
                @csrf
                <div style="display: grid; gap: 20px;">
                    <div class="form-card">
                        <div class="form-card-title">Merchandise & Quantity</div>
                        <div style="display: grid; gap: 16px;">
                            <label>Product & Size
                                <select name="product_variant_id" required>
                                    @foreach ($variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $variant->product?->name }} · Size {{ $variant->size }} · {{ $variant->stock }} left</option>
                                    @endforeach
                                </select>
                            </label>
                            <div class="sub-grid-2">
                                <label>Qty
                                    <input name="quantity" type="number" min="1" value="1" required>
                                </label>
                                <label>Shipping Cost
                                    <input name="shipping_cost" type="number" min="0" value="0" required>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="form-card-title">Customer & Logistics Information</div>
                        <div style="display: grid; gap: 16px;">
                            <div class="sub-grid-2">
                                <label>Customer Name
                                    <input name="customer_name" placeholder="John Doe" required>
                                </label>
                                <label>Email Address
                                    <input name="customer_email" type="email" placeholder="john@example.com">
                                </label>
                            </div>
                            <div class="sub-grid-2">
                                <label>Phone Number
                                    <input name="customer_phone" placeholder="08123456789">
                                </label>
                                <label>Destination City Name
                                    <input name="destination_city_name" placeholder="Jakarta Selatan">
                                </label>
                            </div>
                            <div class="sub-grid-2">
                                <label>RajaOngkir City ID
                                    <input name="destination_city_id" placeholder="153">
                                </label>
                                <label>Courier Code
                                    <input name="courier" placeholder="jne">
                                </label>
                            </div>
                            <label>Courier Service
                                <input name="courier_service" placeholder="REG">
                            </label>
                            <label>Shipping Address
                                <textarea name="shipping_address" placeholder="Enter complete home or office shipping address..."></textarea>
                            </label>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        <button class="secondary-button" type="button" onclick="closeModal('add-order-modal')">Discard</button>
                        <button class="button" type="submit">Create Pending Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODALS: UPDATE TRACKING RESI
     ========================================== -->
@foreach ($orders as $order)
    <div class="modal-overlay" id="tracking-modal-{{ $order->id }}">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Update Tracking: {{ $order->order_number }}</h3>
                <button onclick="closeModal('tracking-modal-{{ $order->id }}')" style="color: var(--muted);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.orders.shipment', $order) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div style="display: grid; gap: 20px;">
                        <div class="form-card">
                            <div class="form-card-title">Order Logistics Details</div>
                            <div style="font-size: 13px; color: var(--charcoal); margin-bottom: 16px;">
                                <strong style="display: block; font-size: 14px;">Customer: {{ $order->customer_name }}</strong>
                                <span style="color: var(--muted);">Grand Total: {{ $order->formatted_grand_total }}</span>
                            </div>
                            <div style="display: grid; gap: 16px;">
                                <div class="sub-grid-2">
                                    <label>Courier Code
                                        <input name="courier" value="{{ $order->shipment?->courier ?: $order->courier }}" required>
                                    </label>
                                    <label>Service Type
                                        <input name="service" value="{{ $order->shipment?->service ?: $order->courier_service }}">
                                    </label>
                                </div>
                                <div class="sub-grid-2">
                                    <label>Tracking Resi (Awb)
                                        <input name="tracking_number" value="{{ $order->shipment?->tracking_number }}" required>
                                    </label>
                                    <label>Fulfillment Status
                                        <select name="status">
                                            <option value="ready_to_ship" @selected(($order->shipment?->status ?: $order->fulfillment_status) === 'ready_to_ship')>Ready to Ship</option>
                                            <option value="shipped" @selected(($order->shipment?->status ?: $order->fulfillment_status) === 'shipped')>Shipped</option>
                                            <option value="delivered" @selected(($order->shipment?->status ?: $order->fulfillment_status) === 'delivered')>Delivered</option>
                                            <option value="returned" @selected(($order->shipment?->status ?: $order->fulfillment_status) === 'returned')>Returned</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                            <button class="secondary-button" type="button" onclick="closeModal('tracking-modal-{{ $order->id }}')">Cancel</button>
                            <button class="button" type="submit">Update Tracking</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on escape key
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const activeModals = document.querySelectorAll('.modal-overlay.active');
            activeModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
</script>
@endsection
