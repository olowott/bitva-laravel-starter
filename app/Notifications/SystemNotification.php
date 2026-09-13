<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public ?string $type = 'info',
        public bool $sendEmail = false,
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = [
            'database',
        ];

        if ($this->sendEmail) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->markdown('mail.system-notification', [
                'recipientName' => $notifiable->name,
                'title' => $this->title,
                'message' => $this->message,
                'actionUrl' => $this->url
                    ? $this->resolveMailUrl()
                    : null,
                'actionLabel' => $this->url
                    ? 'View Details'
                    : null,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
        ];
    }

    private function resolveMailUrl(): string
    {
        if (!$this->url) {
            return url('/');
        }

        if (
            str_starts_with($this->url, '/')
            && !str_starts_with($this->url, '//')
        ) {
            return url($this->url);
        }

        return url('/');
    }
}
