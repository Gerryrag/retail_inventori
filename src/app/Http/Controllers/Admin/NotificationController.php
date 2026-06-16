<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('admin.notifications.index', [
            'notifications' => AdminNotification::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        AdminNotification::create($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,danger'],
        ]));

        return redirect()->route('admin.notifications.index')->with('status', 'Notifikasi berhasil dibuat.');
    }

    public function markAsRead(AdminNotification $notification): RedirectResponse
    {
        $notification->update(['is_read' => true]);

        return redirect()->route('admin.notifications.index')->with('status', 'Notifikasi ditandai sudah dibaca.');
    }

    public function destroy(AdminNotification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')->with('status', 'Notifikasi berhasil dihapus.');
    }
}
