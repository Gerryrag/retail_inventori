@extends('admin.layout')

@section('title', 'Notifikasi')
@section('subtitle', 'Buat dan kelola notifikasi internal admin.')

@section('content')
    <section class="grid">
        <div class="panel">
            <div class="panel-head"><div><h2>Daftar Notifikasi</h2><span>Data tersimpan di tabel admin_notifications.</span></div></div>
            <div class="list">
                @forelse ($notifications as $notification)
                    <article class="notice">
                        <strong>{{ $notification->title }} <span class="badge {{ $notification->type === 'danger' ? 'danger' : ($notification->type === 'warning' ? 'warn' : '') }}">{{ $notification->is_read ? 'Dibaca' : 'Baru' }}</span></strong>
                        <p>{{ $notification->message }}</p>
                        <div class="inline-actions">
                            @unless ($notification->is_read)<form method="POST" action="{{ route('admin.notifications.read', $notification) }}">@csrf @method('PATCH')<button class="secondary-button" type="submit">Tandai Dibaca</button></form>@endunless
                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}">@csrf @method('DELETE')<button class="danger-button" type="submit">Hapus</button></form>
                        </div>
                    </article>
                @empty
                    <article class="notice"><strong>Belum ada notifikasi</strong><p>Buat notifikasi pertama dari form di samping.</p></article>
                @endforelse
            </div>
        </div>
        <div class="panel">
            <div class="panel-head"><div><h2>Buat Notifikasi</h2><span>Untuk catatan internal admin.</span></div></div>
            <form class="form-grid" method="POST" action="{{ route('admin.notifications.store') }}">
                @csrf
                <label class="full">Judul<input name="title" required></label>
                <label>Jenis<select name="type"><option value="info">Info</option><option value="warning">Warning</option><option value="danger">Danger</option></select></label>
                <label class="full">Pesan<textarea name="message" required></textarea></label>
                <div class="full"><button class="button" type="submit">Simpan Notifikasi</button></div>
            </form>
        </div>
    </section>
@endsection
