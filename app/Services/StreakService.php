<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\RestDay;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class StreakService
{
    /**
     * Daily habit: consecutive days (ending today, or yesterday if today is still open)
     * on which the habit reached its daily target; a rest day neither counts nor breaks it.
     * Weekly habit ("3 days a week"): consecutive weeks (Monday to Sunday) that reached the goal.
     */
    public function habitStreak(Habit $habit, CarbonImmutable $today): int
    {
        $dates = $habit->logs()
            ->where('value', '>=', $habit->target_count)
            ->pluck('logged_date')
            ->map(fn ($d) => $this->day($d));

        if ($habit->isWeekly()) {
            return $this->currentWeeks($dates, $habit->weekly_goal, $today);
        }

        return $this->current($dates, $today, $this->restDates($habit->user_id));
    }

    /**
     * Days this week (Monday to Sunday) on which each weekly habit reached its target.
     *
     * @param  Collection<int, Habit>  $habits
     * @return array<int, int> habit id => days done
     */
    public function weekCounts(Collection $habits, CarbonImmutable $day): array
    {
        $ids = $habits->filter->isWeekly()->pluck('id');

        if ($ids->isEmpty()) {
            return [];
        }

        return HabitLog::query()
            ->join('habits', 'habits.id', '=', 'habit_logs.habit_id')
            ->whereIn('habit_logs.habit_id', $ids)
            ->whereColumn('habit_logs.value', '>=', 'habits.target_count')
            ->whereBetween('habit_logs.logged_date', [$day->startOfWeek()->toDateString(), $day->endOfWeek()->toDateString()])
            ->groupBy('habit_logs.habit_id')
            ->selectRaw('habit_logs.habit_id as habit_id, count(distinct habit_logs.logged_date) as days')
            ->pluck('days', 'habit_id')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    /**
     * Overall streak: days with at least one completed habit.
     *
     * @return array{current: int, best: int}
     */
    public function overall(User $user, CarbonImmutable $today): array
    {
        $dates = $this->completedDates($user);
        $rest = $this->restDates($user->id);

        return [
            'current' => $this->current($dates, $today, $rest),
            'best' => $this->best($dates, $rest),
        ];
    }

    /**
     * Dates (Y-m-d) the user chose to rest.
     *
     * @return Collection<int, string>
     */
    public function restDates(int $userId): Collection
    {
        return RestDay::query()
            ->where('user_id', $userId)
            ->pluck('rest_date')
            ->map(fn ($d) => $this->day($d))
            ->values();
    }

    /**
     * Distinct dates (Y-m-d) on which the user completed at least one habit.
     *
     * @return Collection<int, string>
     */
    public function completedDates(User $user): Collection
    {
        return HabitLog::query()
            ->join('habits', 'habits.id', '=', 'habit_logs.habit_id')
            ->where('habits.user_id', $user->id)
            ->whereNull('habits.deleted_at')
            ->whereColumn('habit_logs.value', '>=', 'habits.target_count')
            ->distinct()
            ->pluck('habit_logs.logged_date')
            ->map(fn ($d) => $this->day($d))
            ->unique()
            ->values();
    }

    /**
     * @param  Collection<int, string>  $dates
     * @param  Collection<int, string>  $rest
     */
    private function current(Collection $dates, CarbonImmutable $today, Collection $rest): int
    {
        $set = $dates->flip();
        $resting = $rest->flip();
        $cursor = $set->has($today->toDateString()) || $resting->has($today->toDateString()) ? $today : $today->subDay();
        $count = 0;

        while (true) {
            $key = $cursor->toDateString();

            if ($set->has($key)) {
                $count++;
            } elseif (! $resting->has($key)) {
                break;
            }

            $cursor = $cursor->subDay();
        }

        return $count;
    }

    /**
     * @param  Collection<int, string>  $dates
     * @param  Collection<int, string>  $rest
     */
    private function best(Collection $dates, Collection $rest): int
    {
        $resting = $rest->flip();
        $best = $run = 0;
        $previous = null;

        foreach ($dates->sort()->values() as $date) {
            $run = $previous && $this->onlyRestBetween($previous, $date, $resting) ? $run + 1 : 1;
            $best = max($best, $run);
            $previous = $date;
        }

        return $best;
    }

    /** True when every day strictly between the two dates is a rest day (or there is none). */
    private function onlyRestBetween(string $from, string $to, Collection $resting): bool
    {
        for ($d = CarbonImmutable::parse($from)->addDay(); $d->toDateString() < $to; $d = $d->addDay()) {
            if (! $resting->has($d->toDateString())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Weeks in a row that reached the goal, ending this week, or last week while this one is still open.
     *
     * @param  Collection<int, string>  $dates
     */
    private function currentWeeks(Collection $dates, int $goal, CarbonImmutable $today): int
    {
        $met = $dates->unique()
            ->groupBy(fn ($d) => CarbonImmutable::parse($d)->startOfWeek()->toDateString())
            ->filter(fn ($days) => $days->count() >= $goal);

        $cursor = $today->startOfWeek();
        if (! $met->has($cursor->toDateString())) {
            $cursor = $cursor->subWeek();
        }

        $count = 0;
        while ($met->has($cursor->toDateString())) {
            $count++;
            $cursor = $cursor->subWeek();
        }

        return $count;
    }

    private function day(mixed $value): string
    {
        return CarbonImmutable::parse($value)->toDateString();
    }
}
