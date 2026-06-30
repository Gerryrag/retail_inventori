@extends('admin.layout')

@section('title', 'Notifikasi')
@section('subtitle', 'Buat dan kelola notifikasi internal admin.')

@section('content')
<style>
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
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: var(--surface);
        border: 1px solid var(--line-light);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
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
    .modal-header h3 { font-size: 18px; font-weight: 600; color: var(--charcoal); }
    .modal-body { padding: 24px; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .notification-item {
        border: 1px solid var(--line-light);
        border-radius: var(--radius);
        padding: 16px 20px;
        background: var(--surface);
        display: grid;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .notification-item:hover { box-shadow: var(--shadow-soft); border-color: var(--line); }
    .notification-item.unread { border-left: 3px solid var(--charcoal); }
    .notification-item.type-danger { border-left-color: var(--rose); }
    .notification-item.type-warning { border-left-color: var(--amber); }
    .notification-item.type-success { border-left-color: var(--emerald); }
    .notification-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: var(--charcoal);
        flex: 1;
    }
    .notification-message {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.6;
    }
    .notification-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
</style>

<!-- Header actions -->
@section('actions')
    <button class="button" onclick="openModal('create-notification-modal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Buat Notifikasi
    </button>
@endsection

<!-- Notifications list -->
<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Daftar Notifikasi</h2>
            <span>Data tersimpan di tabel admin_notifications.</span>
        </div>
        <span class="badge">{{ $notifications->count() }} notifikasi</span>
    </div>
    <div class="list">
        @forelse ($notifications as $notification)
            <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }} type-{{ $notification->type }}">
                <div class="notification-meta">
                    <span class="notification-title">{{ $notification->title }}</span>
                    <span class="badge {{ $notification->type === 'danger' ? 'danger' : ($notification->type === 'warning' ? 'warn' : ($notification->type === 'success' ? 'success' : '')) }}">
                        {{ $notification->type }}
                    </span>
                    <span class="badge {{ $notification->is_read ? '' : 'success' }}">
                        {{ $notification->is_read ? 'Dibaca' : 'Baru' }}
                    </span>
                </div>
                <p class="notification-message">{{ $notification->message }}</p>
                <div class="notification-footer">
                    @unless ($notification->is_read)
                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button class="secondary-button" type="submit" style="min-height: 28px; font-size: 11px; padding: 0 12px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M20 6 9 17l-5-5"/></svg>
                                Tandai Dibaca
                            </button>
                        </form>
                    @endunless
                    <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}" onsubmit="return confirm('Hapus notifikasi ini?')" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="ghost-button" type="submit" style="color: var(--rose); min-height: 28px; font-size: 11px; padding: 0 8px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="notice">
                <strong>Belum ada notifikasi</strong>
                <p>Buat notifikasi pertama dengan menekan tombol "Buat Notifikasi" di pojok kanan atas.</p>
            </div>
        @endforelse
    </div>
</section>

<!-- MODAL: Create Notification -->
<div class="modal-overlay" id="create-notification-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Buat Notifikasi Baru</h3>
            <button onclick="closeModal('create-notification-modal')" style="color: var(--muted);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.notifications.store') }}">
                @csrf
                <div style="display: grid; gap: 16px;">
                    <label class="full">Judul Notifikasi
                        <input name="title" placeholder="Judul singkat dan jelas..." required>
                    </label>
                    <label>Jenis
                        <select name="type">
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Danger</option>
                        </select>
                    </label>
                    <label class="full">Pesan
                        <textarea name="message" placeholder="Deskripsi lengkap notifikasi..." required></textarea>
                    </label>
                    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 8px;">
                        <button class="secondary-button" type="button" onclick="closeModal('create-notification-modal')">Batal</button>
                        <button class="button" type="submit">Simpan Notifikasi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });
</script>
@endsection
