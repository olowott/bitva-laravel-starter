<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_notifications(): void
    {
        $user = User::factory()->create();

        $user->notify(
            new SystemNotification(
                title: 'Test notification',
                message: 'This is a test message.'
            )
        );

        $response = $this->actingAs($user)
            ->get(route('admin.notifications.index'));

        $response
            ->assertOk()
            ->assertSee('Test notification')
            ->assertSee('This is a test message.');
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $otherUser->notify(
            new SystemNotification(
                title: 'Private notification',
                message: 'Only this user should see this.'
            )
        );

        $notification = $otherUser
            ->notifications()
            ->first();

        $response = $this->actingAs($user)
            ->patch(
                route(
                    'admin.notifications.read',
                    $notification
                )
            );

        $response->assertNotFound();

        $this->assertNull(
            $notification->fresh()->read_at
        );
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();

        $user->notify(
            new SystemNotification(
                title: 'Unread notification',
                message: 'Please read me.'
            )
        );

        $notification = $user
            ->notifications()
            ->first();

        $response = $this->actingAs($user)
            ->patch(
                route(
                    'admin.notifications.read',
                    $notification
                )
            );

        $response->assertRedirect();

        $this->assertNotNull(
            $notification->fresh()->read_at
        );
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();

        $user->notify(
            new SystemNotification(
                title: 'First',
                message: 'First notification.'
            )
        );

        $user->notify(
            new SystemNotification(
                title: 'Second',
                message: 'Second notification.'
            )
        );

        $response = $this->actingAs($user)
            ->patch(
                route(
                    'admin.notifications.read-all'
                )
            );

        $response->assertRedirect();

        $this->assertSame(
            0,
            $user
                ->fresh()
                ->unreadNotifications()
                ->count()
        );
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $response = $this->get(
            route('admin.notifications.index')
        );

        $response->assertRedirect(
            route('login')
        );
    }
}
