<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_notification_uses_database_channel_by_default(): void
    {
        $user = User::factory()->create();

        $notification = new SystemNotification(
            title: 'Test Notification',
            message: 'This is a test notification.'
        );

        $this->assertSame(
            ['database'],
            $notification->via($user)
        );
    }

    public function test_system_notification_can_use_database_and_mail_channels(): void
    {
        $user = User::factory()->create();

        $notification = new SystemNotification(
            title: 'Test Notification',
            message: 'This is a test notification.',
            sendEmail: true,
        );

        $this->assertSame(
            ['database', 'mail'],
            $notification->via($user)
        );
    }

    public function test_mail_notification_contains_expected_content(): void
    {
        $user = User::factory()->create([
            'name' => 'Tope Johnson',
        ]);

        $notification = new SystemNotification(
            title: 'Account Updated',
            message: 'Your account information was updated.',
            url: '/profile',
            sendEmail: true,
        );

        $mail = $notification->toMail($user);

        $this->assertSame(
            'Account Updated',
            $mail->subject
        );

        $this->assertSame(
            'mail.system-notification',
            $mail->markdown
        );

        $this->assertSame(
            'Your account information was updated.',
            $mail->viewData['message']
        );

        $this->assertSame(
            'Tope Johnson',
            $mail->viewData['recipientName']
        );

        $this->assertSame(
            url('/profile'),
            $mail->viewData['actionUrl']
        );
    }

    public function test_external_notification_url_falls_back_to_application_home(): void
    {
        $user = User::factory()->create();

        $notification = new SystemNotification(
            title: 'Unsafe URL',
            message: 'Testing unsafe URL.',
            url: 'https://evil.example.com',
            sendEmail: true,
        );

        $mail = $notification->toMail($user);

        $this->assertSame(
            url('/'),
            $mail->viewData['actionUrl']
        );
    }

    public function test_protocol_relative_notification_url_falls_back_to_application_home(): void
    {
        $user = User::factory()->create();

        $notification = new SystemNotification(
            title: 'Unsafe URL',
            message: 'Testing unsafe URL.',
            url: '//evil.example.com',
            sendEmail: true,
        );

        $mail = $notification->toMail($user);

        $this->assertSame(
            url('/'),
            $mail->viewData['actionUrl']
        );
    }
}
