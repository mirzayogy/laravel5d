<?php

namespace App\Services;

use App\Models\HabitLog;
use App\Models\User;
use Carbon\CarbonImmutable;

class CalendarService
{
    /**
     * Build a Monday-first month grid with the completion ratio of every day.
     *
     * @return array<int, array<int, array<string, mixed>>> weeks of days
     */
    public function month(User $user, CarbonImmutable $month, CarbonImmutable $today): array
    {
        $first = $month->startOfMonth();
        $gridStart = $first->startOfWeek(CarbonImmutable::MONDAY);
        $gridEnd = $first->endOfMonth()->endOfWeek(CarbonImmutable::SUNDAY);

        $habits = $user->habits()->get(['id', 'target_count', 'weekly_goal', 'created_at', 'is_active']);
        $logs = HabitLog::query()
            ->whereIn('habit_id', $habits->pluck('id'))
            ->whereBetween('logged_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get(['habit_id', 'logged_date', 'value'])
            ->groupBy(fn ($l) => $l->logged_date->toDateString());

        $moods = $user->moodEntries()
            ->whereBetween('entry_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->get(['entry_date', 'mood_level'])
            ->mapWithKeys(fn ($m) => [$m->entry_date->toDateString() => $m->mood_level]);

        $targets = $habits->keyBy('id');
        $weeks = [];

        for ($day = $gridStart; $day <= $gridEnd; $day = $day->addDay()) {
            $key = $day->toDateString();
            // A day counts the active daily habits that existed then, plus any habit logged on it (back-filled days).
            // A weekly habit is not expected on every day, so it only counts on the days it was logged.
            $dayLogs = $logs[$key] ?? collect();
            $total = $habits
                ->filter(fn ($h) => ($h->is_active && ! $h->isWeekly() && $h->created_at->startOfDay() <= $day) || $dayLogs->contains('habit_id', $h->id))
                ->count();
            $done = $dayLogs
                ->filter(fn ($l) => $targets->has($l->habit_id) && $l->value >= $targets[$l->habit_id]->target_count)
                ->count();

            $weeks[(int) floor($gridStart->diffInDays($day) / 7)][] = [
                'date' => $key,
                'day' => $day->day,
                'in_month' => $day->month === $first->month,
                'is_today' => $day->equalTo($today),
                'is_future' => $day > $today,
                'total' => $total,
                'done' => $done,
                'ratio' => $total > 0 ? min(1, $done / $total) : 0,
                'mood' => $moods[$key] ?? null,
            ];
        }

        return $weeks;
    }
}
