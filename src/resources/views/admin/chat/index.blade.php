@extends('admin.layout')

@section('title', 'Live Chat')
@section('subtitle', 'Split-screen customer conversations for lightweight daily support.')

@section('content')
<style>
    /* Chat-page-specific refinements */
    .chat-thread-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 16px 8px;
        border-bottom: 1px solid var(--line-light);
    }
    .chat-thread-search {
        padding: 8px 12px;
    }
    .chat-thread-search input {
        min-height: 34px;
        padding: 0 10px;
        font-size: 12px;
        background: var(--surface);
    }
    .bubble strong {
        display: block;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 4px;
        opacity: 0.65;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .bubble p {
        margin: 0;
        font-size: 13px;
        line-height: 1.6;
    }
    .chat-input {
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 8px;
        padding: 12px 16px;
        border-top: 1px solid var(--line-light);
        background: var(--surface);
        align-items: end;
    }
    .chat-input input[type="text"] {
        min-height: 40px;
        padding: 0 12px;
        font-size: 13px;
    }
    .chat-input .sender-input {
        display: none;
    }
    .chat-status {
        font-size: 11px;
        color: var(--muted);
        padding: 4px 0;
    }
    .online-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: var(--emerald);
        margin-right: 4px;
    }
</style>

<section class="panel" style="padding: 0; overflow: hidden;">
    <div class="chat-shell" style="border: none; border-radius: 0;">

        {{-- ====== LEFT: Thread List ====== --}}
        <div class="chat-list">
            <div class="chat-thread-header">
                <div>
                    <h2 style="font-size: 14px; font-weight: 600; color: var(--charcoal); margin: 0;">Threads</h2>
                    <span style="font-size: 11px; color: var(--muted);">{{ $threads->count() }} customer groups</span>
                </div>
            </div>
            <div class="chat-thread-search">
                <input type="text" id="thread-search" placeholder="Search threads..." oninput="filterThreads(this.value)">
            </div>

            @forelse ($threads as $sender => $threadMessages)
                @php $lastMsg = $threadMessages->last(); @endphp
                <a class="chat-thread {{ $loop->first ? 'active' : '' }}"
                   href="#chat-window"
                   data-sender="{{ $sender }}"
                   onclick="switchThread(this, '{{ addslashes($sender) }}')">
                    <strong>
                        <span class="unread-dot"></span>
                        {{ $sender }}
                    </strong>
                    <p>{{ \Illuminate\Support\Str::limit($lastMsg->message, 60) }}</p>
                    <p>{{ $lastMsg->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <div class="chat-thread active">
                    <strong>No conversations</strong>
                    <p>Messages from customer channels will appear here.</p>
                </div>
            @endforelse
        </div>

        {{-- ====== RIGHT: Chat Window ====== --}}
        <div class="chat-window" id="chat-window">
            <div class="chat-window-head">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; border-radius: 999px; background: var(--surface-soft); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; color: var(--charcoal); flex-shrink: 0;" id="chat-avatar">
                        @php $firstThread = $threads->keys()->first() ?? 'C'; @endphp
                        {{ strtoupper(substr($firstThread, 0, 2)) }}
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 15px; font-weight: 600; color: var(--charcoal);" id="active-sender-name">
                            {{ $threads->keys()->first() ?? 'No thread selected' }}
                        </h2>
                        <p class="chat-status"><span class="online-dot"></span>Admin · Active conversation</p>
                    </div>
                </div>
            </div>

            <div class="messages" id="messages-container">
                @forelse ($messages as $message)
                    <article class="bubble {{ strtolower($message->sender_name) === 'admin' ? 'admin' : '' }}"
                             data-thread="{{ $message->sender_name }}">
                        <strong>{{ $message->sender_name }}</strong>
                        <p>{{ $message->message }}</p>
                        <span style="font-size: 10px; opacity: 0.5; display: block; margin-top: 4px;">{{ $message->created_at->format('d M Y H:i') }}</span>
                    </article>
                @empty
                    <article class="bubble">
                        <strong>System</strong>
                        <p>No messages yet. Start a conversation below.</p>
                    </article>
                @endforelse
            </div>

            <form class="chat-input" method="POST" action="{{ route('admin.chat.store') }}" id="chat-form">
                @csrf
                <input class="sender-input" name="sender_name" id="sender-hidden" value="Admin" required>
                <input type="text" name="message" placeholder="Type a reply to the selected customer..." required>
                <button class="secondary-button" type="button" onclick="switchSender('Customer')">As Customer</button>
                <button class="button" type="submit">Send</button>
            </form>
        </div>
    </div>
</section>

<script>
    // Scroll messages to bottom on load
    const msgContainer = document.getElementById('messages-container');
    if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;

    // Switch thread (visual only — form POST always goes to same endpoint)
    function switchThread(el, senderName) {
        document.querySelectorAll('.chat-thread').forEach(t => t.classList.remove('active'));
        el.classList.add('active');

        // Update header
        const nameEl = document.getElementById('active-sender-name');
        const avatarEl = document.getElementById('chat-avatar');
        if (nameEl) nameEl.textContent = senderName;
        if (avatarEl) avatarEl.textContent = senderName.substring(0, 2).toUpperCase();
    }

    // Toggle sender name (for testing as Customer vs Admin)
    function switchSender(name) {
        const hidden = document.getElementById('sender-hidden');
        const btn = document.querySelector('.chat-input .secondary-button');
        if (hidden.value === 'Admin') {
            hidden.value = 'Customer';
            btn.textContent = 'As Admin';
        } else {
            hidden.value = 'Admin';
            btn.textContent = 'As Customer';
        }
    }

    // Search/filter thread list
    function filterThreads(query) {
        const threads = document.querySelectorAll('.chat-thread');
        threads.forEach(thread => {
            const text = thread.textContent.toLowerCase();
            thread.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
        });
    }
</script>
@endsection
