<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class NotificationComposerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate([
            'name' => 'notifications.send',
            'guard_name' => 'web',
        ]);
    }

    public function test_authorized_user_can_view_notification_composer(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('notifications.send');

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.notifications.create'));

        $response->assertOk();
        $response->assertSee('Send Notification');
    }

    public function test_unauthorized_user_cannot_view_notification_composer(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('admin.notifications.create'));

        $response->assertForbidden();
    }

    public function test_authorized_user_can_send_database_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->givePermissionTo('notifications.send');

        $recipient = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.notifications.send'), [
                'user_id' => $recipient->id,
                'title' => 'System Update',
                'message' => 'Your account has been updated.',
                'type' => 'info',
            ]);

        $response->assertRedirect(
            route('admin.notifications.create')
        );

        Notification::assertSentTo(
            $recipient,
            SystemNotification::class,
            function (SystemNotification $notification) {
                return
                    $notification->title === 'System Update'
                    && $notification->message === 'Your account has been updated.'
                    && $notification->type === 'info'
                    && $notification->sendEmail === false;
            }
        );
    }

    public function test_authorized_user_can_send_notification_with_email(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->givePermissionTo('notifications.send');

        $recipient = User::factory()->create([
            'is_active' => true,
        ]);

        $this
            ->actingAs($admin)
            ->post(route('admin.notifications.send'), [
                'user_id' => $recipient->id,
                'title' => 'Important Update',
                'message' => 'Please review your account.',
                'type' => 'warning',
                'url' => '/profile',
                'send_email' => 1,
            ]);

        Notification::assertSentTo(
            $recipient,
            SystemNotification::class,
            function (SystemNotification $notification) {
                return
                    $notification->sendEmail === true
                    && $notification->url === '/profile';
            }
        );
    }

    public function test_external_notification_url_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('notifications.send');

        $recipient = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.notifications.send'), [
                'user_id' => $recipient->id,
                'title' => 'Unsafe URL',
                'message' => 'Testing validation.',
                'type' => 'info',
                'url' => 'https://evil.example.com',
            ]);

        $response->assertSessionHasErrors('url');
    }

    public function test_protocol_relative_notification_url_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->givePermissionTo('notifications.send');

        $recipient = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.notifications.send'), [
                'user_id' => $recipient->id,
                'title' => 'Unsafe URL',
                'message' => 'Testing validation.',
                'type' => 'info',
                'url' => '//evil.example.com',
            ]);

        $response->assertSessionHasErrors('url');
    }

    public function test_notification_sending_is_rate_limited(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->givePermissionTo('notifications.send');

        $recipient = User::factory()->create([
            'is_active' => true,
        ]);

        $payload = [
            'user_id' => $recipient->id,
            'title' => 'Rate Limit Test',
            'message' => 'Testing notification rate limiting.',
            'type' => 'info',
        ];

        for ($i = 0; $i < 10; $i++) {
            $this
                ->actingAs($admin)
                ->post(
                    route('admin.notifications.send'),
                    $payload
                )
                ->assertRedirect(
                    route('admin.notifications.create')
                );
        }

        $this
            ->actingAs($admin)
            ->post(
                route('admin.notifications.send'),
                $payload
            )
            ->assertTooManyRequests();
    }
}
