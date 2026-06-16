@extends('admin.layout')

@section('title', 'Daftar Barang')
@section('subtitle', 'Tambah, edit, hapus barang, stok, harga, dan URL gambar Cloudinary.')
@section('actions')
    <a class="button" href="#tambah">Tambah Barang</a>
@endsection

@section('content')
    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Daftar Barang</h2><span>Produk aktif akan tampil di landing user.</span></div><span class="badge">{{ $products->count() }} barang</span></div>
            <table>
                <thead><tr><th>Gambar</th><th>Produk</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><img class="thumb" src="{{ $product->image_url ?: 'https://placehold.co/320x240/eaf5ed/07583c?text=Produk' }}" alt="{{ $product->name }}"></td>
                            <td><strong>{{ $product->name }}</strong><br><span>{{ $product->category ?: 'Tanpa kategori' }}</span></td>
                            <td>{{ $product->formatted_price }}</td>
                            <td>{{ $product->stock }}</td>
                            <td><span class="badge">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="panel" id="tambah">
            <div class="panel-head"><div><h2>Tambah Barang</h2><span>Tempel URL Cloudinary di kolom gambar.</span></div></div>
            <form class="form-grid" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <label>Nama Produk<input name="name" value="{{ old('name') }}" required></label>
                <label>Kategori<input name="category" value="{{ old('category') }}"></label>
                <label>Harga<input name="price" type="number" min="0" value="{{ old('price', 0) }}" required></label>
                <label>Stok<input name="stock" type="number" min="0" value="{{ old('stock', 0) }}" required></label>
                <label class="full">Upload Gambar ke Cloudinary<input name="image" type="file" accept="image/*"></label>
                <label class="full">Atau URL Gambar Cloudinary<input name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://res.cloudinary.com/..."></label>
                <label class="full">Deskripsi<textarea name="description">{{ old('description') }}</textarea></label>
                <label>Status<select name="is_active"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></label>
                <div class="full"><button class="button" type="submit">Simpan Barang</button></div>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head"><div><h2>Edit dan Hapus Barang</h2><span>Perubahan langsung tersimpan di Neon.tech.</span></div></div>
        @forelse ($products as $product)
            <form class="form-grid" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <label>Nama<input name="name" value="{{ $product->name }}" required></label>
                <label>Kategori<input name="category" value="{{ $product->category }}"></label>
                <label>Harga<input name="price" type="number" min="0" value="{{ $product->price }}" required></label>
                <label>Stok<input name="stock" type="number" min="0" value="{{ $product->stock }}" required></label>
                <label class="full">Upload Gambar Baru ke Cloudinary<input name="image" type="file" accept="image/*"></label>
                <label class="full">Atau URL Gambar Cloudinary<input name="image_url" type="url" value="{{ $product->image_url }}"></label>
                <label class="full">Deskripsi<textarea name="description">{{ $product->description }}</textarea></label>
                <label>Status<select name="is_active"><option value="1" @selected($product->is_active)>Aktif</option><option value="0" @selected(! $product->is_active)>Nonaktif</option></select></label>
                <div class="full inline-actions"><button class="button" type="submit">Update {{ $product->name }}</button></div>
            </form>
            <form class="form-grid" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus barang ini?')">
                @csrf
                @method('DELETE')
                <div class="full"><button class="danger-button" type="submit">Hapus {{ $product->name }}</button></div>
            </form>
        @empty
            <div class="list"><article class="notice"><strong>Belum ada barang</strong><p>Tambahkan barang pertama.</p></article></div>
        @endforelse
    </section>
@endsection
