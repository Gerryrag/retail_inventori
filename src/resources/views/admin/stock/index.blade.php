@extends('admin.layout')

@section('title', 'Pencatatan')
@section('subtitle', 'Catat barang masuk, keluar, dan koreksi stok.')

@section('content')
    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Form Pencatatan</h2><span>Stok produk akan berubah otomatis.</span></div></div>
            <form class="form-grid" method="POST" action="{{ route('admin.stock.store') }}">
                @csrf
                <label>Produk<select name="product_id" required>@foreach ($products as $product)<option value="{{ $product->id }}">{{ $product->name }} · stok {{ $product->stock }}</option>@endforeach</select></label>
                <label>Jenis<select name="type"><option value="in">Barang Masuk</option><option value="out">Barang Keluar</option><option value="correction">Koreksi Stok</option></select></label>
                <label>Jumlah<input name="quantity" type="number" min="0" required></label>
                <label class="full">Catatan<textarea name="note" placeholder="Supplier, alasan koreksi, atau keterangan lain"></textarea></label>
                <div class="full"><button class="button" type="submit">Simpan Pencatatan</button></div>
            </form>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Petunjuk</h2><span>Mode pencatatan stok.</span></div></div>
            <div class="list">
                <article class="notice"><strong>Barang Masuk</strong><p>Menambah stok produk.</p></article>
                <article class="notice"><strong>Barang Keluar</strong><p>Mengurangi stok produk, tidak bisa minus.</p></article>
                <article class="notice"><strong>Koreksi Stok</strong><p>Mengatur stok akhir menjadi angka jumlah.</p></article>
            </div>
        </div>
    </section>
    <section class="panel">
        <div class="panel-head"><div><h2>Riwayat Pencatatan</h2><span>50 transaksi stok terbaru.</span></div></div>
        <table>
            <thead><tr><th>Tanggal</th><th>Produk</th><th>Jenis</th><th>Jumlah</th><th>Sebelum</th><th>Sesudah</th><th>Catatan</th></tr></thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr><td>{{ $movement->created_at->format('d M Y H:i') }}</td><td>{{ $movement->product?->name }}</td><td>{{ $movement->type }}</td><td>{{ $movement->quantity }}</td><td>{{ $movement->stock_before }}</td><td>{{ $movement->stock_after }}</td><td>{{ $movement->note }}</td></tr>
                @empty
                    <tr><td colspan="7">Belum ada pencatatan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
