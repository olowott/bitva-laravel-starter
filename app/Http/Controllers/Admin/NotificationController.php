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

        $url = data_get(
            $notification->data,
            'url'
        );

        return $url
            ? redirect()->to($url)
            : back();
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
