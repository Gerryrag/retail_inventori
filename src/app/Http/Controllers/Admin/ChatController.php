<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $messages = ChatMessage::query()->latest()->limit(50)->get()->reverse()->values();

        return view('admin.chat.index', [
            'messages' => $messages,
            'threads' => $messages->groupBy('sender_name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sender_name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        ChatMessage::create($data);

        return redirect()->route('admin.chat.index')->with('status', 'Pesan berhasil dikirim.');
    }
}
