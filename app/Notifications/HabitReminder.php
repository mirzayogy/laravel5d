<?php

namespace App\Notifications;

use App\Models\Habit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * "Time for Deep work": one email per person per minute, listing every habit due.
 */
class HabitReminder extends Notification
{
    /** @param  Collection<int, Habit>  $habits */
    public function __construct(public Collection $habits) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = $this->habits->count();
        $first = explode(' ', trim($notifiable->name))[0];

        $mail = (new MailMessage)
            ->subject($count === 1 ? __('Time for :habit', ['habit' => $this->habits->first()->name]) : __(':n habits are waiting for you', ['n' => $count]))
            ->greeting(__('Hi :name,', ['name' => $first]))
            ->line($count === 1 ? __('A habit is due now:') : __('These habits are due now:'));

        foreach ($this->habits as $habit) {
            $mail->line("• {$habit->name} ({$habit->target_label})");
        }

        return $mail
            ->action(__('Open Habitude'), route('dashboard'))
            ->line(__('You get this email because reminders are on for these habits. You can turn emails off in your profile.'));
    }
}
