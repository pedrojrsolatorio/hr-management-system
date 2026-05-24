<?php

namespace App\Http\Controllers;

use Illuminate\Http\{RedirectResponse};
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(string $id): RedirectResponse
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first()
            ?->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }
}
