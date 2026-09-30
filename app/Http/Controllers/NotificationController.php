<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function index(): View {
        $notifications = auth()->user()
        ->notifications()
        ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id): RedirectResponse
    {
        auth()->user()
        ->notifications()
        ->where('id', $id)
        ->update(['is_read' => true]);

        return back();
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()
            ->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }
}
