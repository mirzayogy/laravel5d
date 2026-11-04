<?php

namespace App\Console\Commands;

use App\Models\HabitLog;
use App\Models\Reminder;
use App\Notifications\HabitReminder;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'habitude:send-reminders';

    protected $description = 'Email the reminders that are due now (run every minute by the scheduler)';

    /** If the scheduler was down for a few minutes, reminders this recent are still sent. */
    private const CATCH_UP_MINUTES = 4;

    public function handle(): int
    {
        $now = CarbonImmutable::now();
        $sent = 0;

        foreach (array_keys(config('habitude.timezones')) as $timezone) {
            $local = $now->setTimezone($timezone);
            $to = $local->format('H:i');
            $from = $local->subMinutes(self::CATCH_UP_MINUTES)->format('H:i');

            if ($from > $to) {
                continue; // the window crosses midnight; those few minutes are not worth the complexity
            }

            $due = Reminder::query()
                ->where('is_enabled', true)
                ->whereBetween('remind_at', [$from, $to.':59'])
                ->where(fn ($q) => $q->whereNull('last_sent_on')->orWhere('last_sent_on', '<', $local->toDateString()))
                ->whereHas('habit', fn ($q) => $q->where('is_active', true)
                    ->whereHas('user.profile', fn ($q) => $q->where('timezone', $timezone)->where('email_reminders', true)))
                ->with('habit.user')
                ->get()
                ->filter(fn ($r) => in_array($local->isoWeekday(), $r->days_of_week ?? [], true));

            if ($due->isEmpty()) {
                continue;
            }

            // A habit that is already done today needs no reminder
            $done = HabitLog::query()
                ->whereIn('habit_id', $due->pluck('habit_id'))
                ->whereDate('logged_date', $local->toDateString())
                ->get()
                ->filter(fn ($log) => $log->value >= $due->firstWhere('habit_id', $log->habit_id)->habit->target_count)
                ->pluck('habit_id');

            $due->reject(fn ($r) => $done->contains($r->habit_id))
                ->groupBy(fn ($r) => $r->habit->user_id)
                ->each(function ($reminders) use (&$sent, $local) {
                    $user = $reminders->first()->habit->user;
                    $user->notify(new HabitReminder($reminders->pluck('habit')->unique('id')->values()));
                    Reminder::whereIn('id', $reminders->pluck('id'))->update(['last_sent_on' => $local->toDateString()]);
                    $sent++;
                });
        }

        $this->info($sent === 0 ? 'No reminders due.' : "Sent {$sent} reminder ".($sent === 1 ? 'email' : 'emails').'.');

        return self::SUCCESS;
    }
}
