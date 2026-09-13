<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request
            ->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view(
            'admin.notifications.index',
            compact('notifications')
        );
    }

    public function read(
        Request $request,
        string $notification
    ): RedirectResponse {
        $notification = $request
            ->user()
            ->notifications()
            ->findOrFail($notification);

        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        if (
            $url &&
            str_starts_with($url, '/') &&
            !str_starts_with($url, '//')
        ) {
            return redirect($url);
        }

        return redirect()
            ->route('admin.notifications.index');
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request
            ->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with(
            'success',
            'All notifications marked as read.'
        );
    }
}
