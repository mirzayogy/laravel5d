<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * A small in-app notification stored in the database.
 * `icon` is the name of a Blade icon, `tone` is one of sky, coral, green, amber.
 */
class AppNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
        public string $icon = 'bell',
        public string $tone = 'sky',
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'icon' => $this->icon,
            'tone' => $this->tone,
        ];
    }
}
