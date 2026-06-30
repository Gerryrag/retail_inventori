@extends('admin.layout')

@section('title', 'Product Inventory')
@section('subtitle', 'Configure your product listings for global distribution.')

@section('actions')
    <button class="button" onclick="openModal('add-product-modal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
    </button>
@endsection

@section('content')
<!-- Local page styling for modals and visual grids -->
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
        max-width: 1100px;
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
    
    /* Asymmetric Form Grid */
    .form-cols {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 24px;
    }
    
    .form-main {
        grid-column: span 8;
        display: grid;
        gap: 20px;
    }
    
    .form-side {
        grid-column: span 4;
        display: grid;
        gap: 20px;
    }
    
    /* Upload Box Style */
    .upload-box {
        border: 2px dashed var(--line);
        border-radius: var(--radius);
        background: var(--surface-soft);
        padding: 32px 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .upload-box:hover {
        border-color: var(--outline);
        background: var(--surface-container);
    }
    
    .upload-box svg {
        color: var(--outline);
        margin-bottom: 12px;
        opacity: 0.7;
    }
    
    .upload-box p {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
    }
    
    .upload-box span {
        font-size: 11px;
        color: var(--muted);
        margin-top: 4px;
    }
    
    .sub-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
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

    .matrix-editor {
        width: 100%;
    }
    
    .matrix-editor-row {
        display: grid;
        grid-template-columns: 60px 1fr auto;
        gap: 12px;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--line-light);
    }
    
    .matrix-editor-row:last-child {
        border-bottom: none;
    }
    
    .matrix-editor-size {
        font-weight: 600;
        color: var(--charcoal);
        font-size: 13px;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from { transform: translateY(16px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    @media (max-width: 900px) {
        .form-main, .form-side {
            grid-column: span 12;
        }
    }
</style>

<!-- Main Table Catalog -->
<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Product Catalog</h2>
            <span>Cloudinary image, SKU, price, weight, and stock by size.</span>
        </div>
        <span class="badge">{{ $products->count() }} products</span>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Variants & Stock</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <div class="inline-actions" style="gap: 16px;">
                                <img class="thumb" src="{{ $product->image_url ?: 'https://placehold.co/320x240/e5e7eb/111827?text=Merch' }}" alt="{{ $product->name }}" style="width: 48px; height: 48px; border-radius: var(--radius); object-fit: cover;">
                                <div>
                                    <strong>{{ $product->name }}</strong><br>
                                    <span>{{ $product->category ?: 'Merchandise' }} · {{ $product->weight_gram }}g</span>
                                </div>
                            </div>
                        </td>
                        <td><code style="font-family: monospace; font-size: 12px; color: var(--muted);">{{ $product->sku }}</code></td>
                        <td><strong>{{ $product->formatted_price }}</strong></td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                @forelse ($product->variants as $variant)
                                    <span class="badge {{ $variant->stock <= 0 ? 'danger' : ($variant->stock <= 5 ? 'warn' : 'success') }}">
                                        {{ $variant->size }} · {{ $variant->stock }}
                                    </span>
                                @empty
                                    <span class="badge danger">No variants</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'success' : 'danger' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="inline-actions" style="justify-content: flex-end; gap: 8px;">
                                <button class="icon-action" onclick="openModal('edit-product-modal-{{ $product->id }}')" title="Edit Product">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product and all variants?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="icon-action" type="submit" title="Delete Product" style="color: var(--rose);">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 32px;">
                            No products yet. Create the first clothing item above.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<!-- ==========================================
     MODAL: ADD NEW PRODUCT
     ========================================== -->
<div class="modal-overlay" id="add-product-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Product</h3>
            <button onclick="closeModal('add-product-modal')" style="color: var(--muted);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-cols">
                    <!-- Left Side: General Info & Imagery -->
                    <div class="form-main">
                        <div class="form-card">
                            <div class="form-card-title">General Information</div>
                            <div style="display: grid; gap: 16px;">
                                <div class="sub-grid-2">
                                    <label>Product SKU
                                        <input name="sku" value="{{ old('sku') }}" placeholder="TEE-LOGO-001" required>
                                    </label>
                                    <label>Product Name
                                        <input name="name" value="{{ old('name') }}" placeholder="Logo T-Shirt" required>
                                    </label>
                                </div>
                                <div class="sub-grid-2">
                                    <label>Category
                                        <input name="category" value="{{ old('category', 'T-Shirt') }}">
                                    </label>
                                    <label>Base Price
                                        <input name="price" type="number" min="0" value="{{ old('price', 0) }}" required>
                                    </label>
                                </div>
                                <div class="sub-grid-2">
                                    <label>Weight (gram)
                                        <input name="weight_gram" type="number" min="0" value="{{ old('weight_gram', 250) }}" required>
                                    </label>
                                    <label>Status
                                        <select name="is_active">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </label>
                                </div>
                                <label>Description
                                    <textarea name="description" placeholder="Describe the craftsmanship, materials, and fit...">{{ old('description') }}</textarea>
                                </label>
                            </div>
                        </div>

                        <div class="form-card">
                            <div class="form-card-title">Product Imagery</div>
                            <div style="display: grid; gap: 16px;">
                                <div class="upload-box" onclick="document.getElementById('add-image-input').click()">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    <p>Upload Primary Image</p>
                                    <span>Recommended size: 2400x1600px (JPG/PNG)</span>
                                    <input id="add-image-input" name="image" type="file" accept="image/*" style="display: none;" onchange="updateUploadLabel(this, 'add-upload-label')">
                                </div>
                                <div id="add-upload-label" style="font-size: 12px; color: var(--emerald); font-weight: 500; text-align: center;"></div>
                                <label>Cloudinary URL (Optional)
                                    <input name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://res.cloudinary.com/...">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Variant Matrix & Logistics -->
                    <div class="form-side">
                        <div class="form-card">
                            <div class="form-card-title">Variant Matrix</div>
                            <div class="matrix-editor">
                                @foreach (['S', 'M', 'L', 'XL'] as $index => $size)
                                    <div class="matrix-editor-row">
                                        <span class="matrix-editor-size">{{ $size }}</span>
                                        <input name="variants[{{ $index }}][size]" type="hidden" value="{{ $size }}">
                                        <input name="variants[{{ $index }}][stock]" type="number" min="0" value="{{ old("variants.$index.stock", 0) }}" placeholder="Stock">
                                        <input name="variants[{{ $index }}][is_active]" type="hidden" value="1">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="form-card" style="display: flex; flex-direction: column; gap: 16px;">
                            <button class="button" type="submit" style="width: 100%;">Publish Product</button>
                            <button class="secondary-button" type="button" onclick="closeModal('add-product-modal')" style="width: 100%;">Discard</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODALS: EDIT PRODUCT
     ========================================== -->
@foreach ($products as $product)
    <div class="modal-overlay" id="edit-product-modal-{{ $product->id }}">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Product: {{ $product->name }}</h3>
                <button onclick="closeModal('edit-product-modal-{{ $product->id }}')" style="color: var(--muted);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
            </div>
            <div class="modal-body">
                <div class="form-cols">
                    <!-- Left Side: Edit Details Form -->
                    <div class="form-main">
                        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-card">
                                <div class="form-card-title">General Information</div>
                                <div style="display: grid; gap: 16px;">
                                    <div class="sub-grid-2">
                                        <label>Product SKU
                                            <input name="sku" value="{{ $product->sku }}" required>
                                        </label>
                                        <label>Product Name
                                            <input name="name" value="{{ $product->name }}" required>
                                        </label>
                                    </div>
                                    <div class="sub-grid-2">
                                        <label>Category
                                            <input name="category" value="{{ $product->category }}">
                                        </label>
                                        <label>Base Price
                                            <input name="price" type="number" min="0" value="{{ $product->price }}" required>
                                        </label>
                                    </div>
                                    <div class="sub-grid-2">
                                        <label>Weight (gram)
                                            <input name="weight_gram" type="number" min="0" value="{{ $product->weight_gram }}" required>
                                        </label>
                                        <label>Status
                                            <select name="is_active">
                                                <option value="1" @selected($product->is_active)>Active</option>
                                                <option value="0" @selected(!$product->is_active)>Inactive</option>
                                            </select>
                                        </label>
                                    </div>
                                    <label>Description
                                        <textarea name="description" rows="5">{{ $product->description }}</textarea>
                                    </label>
                                </div>
                            </div>

                            <div class="form-card" style="margin-top: 20px;">
                                <div class="form-card-title">Product Imagery</div>
                                <div style="display: grid; gap: 16px;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <img src="{{ $product->image_url ?: 'https://placehold.co/320x240/e5e7eb/111827?text=No+Image' }}" alt="Current Image" style="width: 80px; height: 60px; border-radius: var(--radius); object-fit: cover; border: 1px solid var(--line-light);">
                                        <div class="upload-box" onclick="document.getElementById('edit-image-input-{{ $product->id }}').click()" style="flex: 1; padding: 16px;">
                                            <p>Change Image</p>
                                            <input id="edit-image-input-{{ $product->id }}" name="image" type="file" accept="image/*" style="display: none;" onchange="updateUploadLabel(this, 'edit-upload-label-{{ $product->id }}')">
                                        </div>
                                    </div>
                                    <div id="edit-upload-label-{{ $product->id }}" style="font-size: 12px; color: var(--emerald); font-weight: 500; text-align: center;"></div>
                                    <label>Cloudinary URL
                                        <input name="image_url" type="url" value="{{ $product->image_url }}">
                                    </label>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 12px; margin-top: 20px;">
                                <button class="button" type="submit" style="flex: 1;">Update Details</button>
                                <button class="secondary-button" type="button" onclick="closeModal('edit-product-modal-{{ $product->id }}')">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Side: Variant Matrix & Sub-forms -->
                    <div class="form-side">
                        <div class="form-card">
                            <div class="form-card-title">Variant Matrix Management</div>
                            <div style="display: grid; gap: 16px;">
                                <!-- List Existing Variants and Forms -->
                                @forelse ($product->variants as $variant)
                                    <form method="POST" action="{{ route('admin.products.variants.update', $variant) }}" style="border-bottom: 1px solid var(--line-light); padding-bottom: 16px;">
                                        @csrf
                                        @method('PUT')
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                            <span class="badge" style="font-weight: 600;">Size {{ $variant->size }}</span>
                                            <span class="badge {{ $variant->stock <= 0 ? 'danger' : ($variant->stock <= 5 ? 'warn' : 'success') }}">{{ $variant->stock }} in stock</span>
                                        </div>
                                        <div style="display: grid; gap: 8px; font-size: 11px;">
                                            <label style="font-size: 11px;">Variant SKU
                                                <input name="sku" value="{{ $variant->sku }}" style="min-height: 32px; padding: 0 8px;" required>
                                            </label>
                                            <div style="display: flex; gap: 8px; align-items: flex-end;">
                                                <label style="font-size: 11px; flex: 1;">Stock
                                                    <input name="stock" type="number" min="0" value="{{ $variant->stock }}" style="min-height: 32px; padding: 0 8px;" required>
                                                </label>
                                                <label style="font-size: 11px; flex: 1;">Status
                                                    <select name="is_active" style="min-height: 32px; padding: 0 8px;">
                                                        <option value="1" @selected($variant->is_active)>Active</option>
                                                        <option value="0" @selected(!$variant->is_active)>Inactive</option>
                                                    </select>
                                                </label>
                                            </div>
                                            <div style="display: flex; gap: 6px; margin-top: 8px;">
                                                <button class="secondary-button" type="submit" style="min-height: 28px; padding: 0 10px; font-size: 11px; flex: 1;">Update Size</button>
                                                <input name="size" type="hidden" value="{{ $variant->size }}">
                                            </div>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('admin.products.variants.destroy', $variant) }}" onsubmit="return confirm('Delete this size variant?')" style="display: inline; margin-top: -12px; margin-bottom: 12px; text-align: right;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="ghost-button" type="submit" style="color: var(--rose); min-height: 24px; font-size: 11px; padding: 0;">Delete Size {{ $variant->size }}</button>
                                    </form>
                                @empty
                                    <div class="badge danger" style="width: 100%; text-align: center; display: block; padding: 12px 0;">No variants defined</div>
                                @endforelse

                                <!-- Add New Variant Form -->
                                <form method="POST" action="{{ route('admin.products.variants.store', $product) }}" style="background: var(--surface-soft); padding: 12px; border-radius: var(--radius); border: 1px dashed var(--line);">
                                    @csrf
                                    <div class="form-card-title" style="margin-bottom: 12px; border-bottom: none; padding-bottom: 0;">Add Size Variant</div>
                                    <div style="display: grid; gap: 8px; font-size: 11px;">
                                        <div style="display: flex; gap: 8px;">
                                            <label style="font-size: 11px; flex: 1;">Size
                                                <input name="size" placeholder="XXL" style="min-height: 32px; padding: 0 8px;" required>
                                            </label>
                                            <label style="font-size: 11px; flex: 1;">Stock
                                                <input name="stock" type="number" min="0" value="0" style="min-height: 32px; padding: 0 8px;" required>
                                            </label>
                                        </div>
                                        <label style="font-size: 11px;">Variant SKU
                                            <input name="sku" placeholder="{{ $product->sku }}-XXL" style="min-height: 32px; padding: 0 8px;" required>
                                        </label>
                                        <input name="is_active" type="hidden" value="1">
                                        <button class="button" type="submit" style="min-height: 28px; font-size: 11px; margin-top: 6px; width: 100%;">Add Variant</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
    
    function updateUploadLabel(input, labelId) {
        const labelEl = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            labelEl.textContent = `Selected: ${input.files[0].name}`;
        } else {
            labelEl.textContent = '';
        }
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
