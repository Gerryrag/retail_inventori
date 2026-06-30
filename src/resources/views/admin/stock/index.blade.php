@extends('admin.layout')

@section('title', 'Pencatatan Stok')
@section('subtitle', 'Catat stok masuk, keluar, dan koreksi per varian ukuran.')

@section('content')
    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Form Pencatatan</h2><span>Perubahan stok hanya berlaku pada ukuran yang dipilih.</span></div></div>
            <form class="form-grid" method="POST" action="{{ route('admin.stock.store') }}">
                @csrf
                <label class="full">
                    Produk & Ukuran
                    <select name="product_variant_id" required>
                        @foreach ($variants as $variant)
                            <option value="{{ $variant->id }}">{{ $variant->product?->name }} · {{ $variant->size }} · stok {{ $variant->stock }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Jenis<select name="type"><option value="in">Barang Masuk</option><option value="out">Barang Keluar</option><option value="correction">Koreksi Stok</option></select></label>
                <label>Jumlah<input name="quantity" type="number" min="0" required></label>
                <label class="full">Catatan<textarea name="note" placeholder="Restock vendor, retur, adjustment admin, atau order testing"></textarea></label>
                <div class="full"><button class="button" type="submit">Simpan Pencatatan</button></div>
            </form>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Mode Stok</h2><span>Khusus inventory clothing.</span></div></div>
            <div class="list">
                <article class="notice"><strong>Barang Masuk</strong><p>Menambah stok varian ukuran tertentu, misalnya Kaos L.</p></article>
                <article class="notice"><strong>Barang Keluar</strong><p>Mengurangi stok varian ukuran tertentu dan tidak bisa minus.</p></article>
                <article class="notice"><strong>Koreksi Stok</strong><p>Mengatur stok akhir varian menjadi angka jumlah.</p></article>
            </div>
        </div>
    </section>
    
    <section class="panel" style="margin-top: 24px;">
        <div class="panel-head"><div><h2>Riwayat Pencatatan</h2><span>50 transaksi stok terbaru.</span></div></div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Tanggal</th><th>Produk</th><th>Ukuran</th><th>Jenis</th><th>Jumlah</th><th>Sebelum</th><th>Sesudah</th><th>Catatan</th></tr></thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                            <td><strong>{{ $movement->product?->name }}</strong></td>
                            <td>{{ $movement->variant?->size ?? '-' }}</td>
                            <td>
                                @if($movement->type === 'in')
                                    <span class="badge success">Masuk</span>
                                @elseif($movement->type === 'out')
                                    <span class="badge danger">Keluar</span>
                                @else
                                    <span class="badge warn">Koreksi</span>
                                @endif
                            </td>
                            <td><strong>{{ $movement->quantity }} pcs</strong></td>
                            <td>{{ $movement->stock_before }}</td>
                            <td>{{ $movement->stock_after }}</td>
                            <td><span style="color: var(--muted); font-size: 12px;">{{ $movement->note }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="color: var(--muted); text-align: center; padding: 24px;">Belum ada pencatatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
