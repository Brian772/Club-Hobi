<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): View {
        $notifications = auth()->user()
        ->notifications()
        ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function updates(): JsonResponse
    {
        $user = auth()->user();
        $notifications = $user->notifications()->take(5)->get();
        $pageNotifications = $user->notifications()->take(20)->get();

        return response()->json([
            'unread_count' => $user->notifications()->where('is_read', false)->count(),
            'html' => view('layouts.partials.notification-items', compact('notifications'))->render(),
            'page_html' => view('notifications.items', ['notifications' => $pageNotifications])->render(),
        ]);
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
