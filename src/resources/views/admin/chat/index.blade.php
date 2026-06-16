@extends('admin.layout')

@section('title', 'Chat')
@section('subtitle', 'Ruang pesan internal sederhana untuk admin/tim toko.')

@section('content')
    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Riwayat Chat</h2><span>50 pesan terbaru.</span></div></div>
            <div class="list">
                @forelse ($messages as $message)
                    <article class="notice"><strong>{{ $message->sender_name }} · {{ $message->created_at->format('d M Y H:i') }}</strong><p>{{ $message->message }}</p></article>
                @empty
                    <article class="notice"><strong>Belum ada pesan</strong><p>Kirim pesan pertama dari form di samping.</p></article>
                @endforelse
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Kirim Pesan</h2><span>Pesan disimpan di database.</span></div></div>
            <form class="form-grid" method="POST" action="{{ route('admin.chat.store') }}">
                @csrf
                <label class="full">Nama Pengirim<input name="sender_name" value="Admin" required></label>
                <label class="full">Pesan<textarea name="message" required></textarea></label>
                <div class="full"><button class="button" type="submit">Kirim</button></div>
            </form>
        </div>
    </section>
@endsection
