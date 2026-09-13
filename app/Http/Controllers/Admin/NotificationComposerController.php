<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationComposerController extends Controller
{
    public function create(): View
    {
        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);

        return view(
            'admin.notifications.create',
            compact('users')
        );
    }

    public function store(
        SendNotificationRequest $request,
        ActivityLogService $activityLogService
    ): RedirectResponse {
        $validated = $request->validated();

        $user = User::findOrFail(
            $validated['user_id']
        );

        $sendEmail = $request->boolean(
            'send_email'
        );

        $user->notify(
            new SystemNotification(
                title: $validated['title'],
                message: $validated['message'],
                url: $validated['url'] ?? null,
                type: $validated['type'],
                sendEmail: $sendEmail,
            )
        );

        $activityLogService->log(
            'Notification sent',
            $user,
            [
                'after' => [
                    'recipient' => $user->email,
                    'title' => $validated['title'],
                    'type' => $validated['type'],
                    'send_email' => $sendEmail,
                ],
            ],
            'created'
        );

        return redirect()
            ->route('admin.notifications.create')
            ->with(
                'success',
                $sendEmail
                ? 'Notification and email queued successfully.'
                : 'Notification queued successfully.'
            );
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->sendEmail) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function viaConnections(): array
    {
        return [
            'database' => 'sync',
            'mail' => config('queue.default'),
        ];
    }
}
