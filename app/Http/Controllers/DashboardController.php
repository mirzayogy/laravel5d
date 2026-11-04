<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HabitLog;
use App\Services\CalendarService;
use App\Services\StreakService;
use App\Support\Greeting;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, StreakService $streaks, CalendarService $calendar)
    {
        $user = $request->user();
        $today = $user->today();

        $date = $this->parseDate($request->query('date'), 'Y-m-d') ?? $today;
        if ($date > $today) {
            $date = $today;
        }
        $month = $this->parseDate($request->query('month'), 'Y-m')?->startOfMonth() ?? $date->startOfMonth();

        $habits = $user->habits()->where('is_active', true)->with(['category', 'tags', 'reminders'])->orderBy('name')->get();
        $logs = HabitLog::whereIn('habit_id', $habits->pluck('id'))
            ->whereDate('logged_date', $date->toDateString())
            ->get()->keyBy('habit_id');

        $weekday = $date->isoWeekday();
        $weekCounts = $streaks->weekCounts($habits, $date);

        // Ordered by the day: habits with a reminder come first by time (morning, afternoon, evening), the rest are "anytime".
        $rows = $habits->map(function ($habit) use ($logs, $streaks, $today, $weekday, $weekCounts) {
            $value = $logs[$habit->id]->value ?? 0;
            $time = $habit->reminders->filter(fn ($r) => $r->is_enabled && in_array($weekday, $r->days_of_week ?? [], true))
                ->map(fn ($r) => substr($r->remind_at, 0, 5))->sort()->first();
            $hour = $time ? (int) substr($time, 0, 2) : null;

            return [
                'habit' => $habit,
                'value' => $value,
                'done' => $value >= $habit->target_count,
                'streak' => $streaks->habitStreak($habit, $today),
                'weekly' => $habit->isWeekly(),
                'week_done' => $weekCounts[$habit->id] ?? 0,
                'timed' => in_array($habit->unit, config('habitude.timed_units'), true),
                'time' => $time,
                'group' => $hour === null ? 'anytime' : ($hour < 12 ? 'morning' : ($hour < 18 ? 'afternoon' : 'evening')),
            ];
        })->sortBy([
            fn ($a, $b) => array_search($a['group'], ['morning', 'afternoon', 'evening', 'anytime']) <=> array_search($b['group'], ['morning', 'afternoon', 'evening', 'anytime']),
            fn ($a, $b) => ($a['time'] ?? '') <=> ($b['time'] ?? ''),
            fn ($a, $b) => $a['habit']->name <=> $b['habit']->name,
        ])->values()->map(fn ($row, $i) => $row + ['index' => $i]);

        $weeks = $calendar->month($user, $month, $today);
        $monthDays = collect($weeks)->flatten(1)->filter(fn ($d) => $d['in_month'] && ! $d['is_future'] && $d['total'] > 0);

        // Has-Many-Through: Category -> Habit -> HabitLog, restricted to this user and month.
        $categoryProgress = Category::query()
            ->withCount(['habitLogs as completed' => fn ($q) => $q
                ->where('habits.user_id', $user->id)
                ->whereColumn('habit_logs.value', '>=', 'habits.target_count')
                ->whereBetween('habit_logs.logged_date', [$month->toDateString(), $month->endOfMonth()->toDateString()]),
            ])
            ->get()
            ->filter(fn ($c) => $c->completed > 0)
            ->sortByDesc('completed')
            ->values();

        $reminders = $habits->flatMap(fn ($h) => $h->reminders->where('is_enabled', true)->map(fn ($r) => [$h, $r]))
            ->filter(fn ($pair) => in_array($weekday, $pair[1]->days_of_week ?? [], true))
            ->sortBy(fn ($pair) => $pair[1]->remind_at)
            ->values();

        // One number per day for the skyline: how many habits reached their target.
        $activity = HabitLog::query()
            ->join('habits', 'habits.id', '=', 'habit_logs.habit_id')
            ->where('habits.user_id', $user->id)
            ->whereNull('habits.deleted_at')
            ->whereColumn('habit_logs.value', '>=', 'habits.target_count')
            ->whereBetween('habit_logs.logged_date', [$today->subDays(400)->toDateString(), $today->toDateString()])
            ->groupBy('habit_logs.logged_date')
            ->selectRaw('habit_logs.logged_date as day, count(*) as total')
            ->get()
            ->map(fn ($row) => ['date' => CarbonImmutable::parse($row->day)->toDateString(), 'count' => (int) $row->total])
            ->values();

        $overall = $streaks->overall($user, $today);
        $restDays = $streaks->restDates($user->id);

        // Yesterday had nothing checked, although at least one habit already existed: offer to fill it in
        $yesterday = $today->subDay();
        $missedYesterday = $date->equalTo($today)
            && $habits->contains(fn ($h) => $h->created_at->startOfDay() <= $yesterday)
            && ! $activity->contains('date', $yesterday->toDateString())
            && ! $restDays->contains($yesterday->toDateString());

        return view('dashboard', [
            'greeting' => Greeting::for($user, CarbonImmutable::now($user->profile?->timezone ?? config('app.timezone')), $rows, $overall),
            'activity' => $activity,
            'missedYesterday' => $missedYesterday,
            'yesterday' => $yesterday,
            'date' => $date,
            'today' => $today,
            'month' => $month,
            'rows' => $rows,
            'weeks' => $weeks,
            'streak' => $overall,
            'restToday' => $restDays->contains($today->toDateString()),
            'restThisWeek' => $user->restDayInWeekOf($today),
            'restYesterdayFree' => ! $user->restDayInWeekOf($yesterday),
            'doneCount' => $rows->where('done', true)->count(),
            'monthRate' => $monthDays->sum('total') > 0 ? (int) round($monthDays->sum('done') / $monthDays->sum('total') * 100) : 0,
            'categoryProgress' => $categoryProgress,
            'mood' => $user->moodEntries()->whereDate('entry_date', $date->toDateString())->first(),
            'reminders' => $reminders,
            'recentAchievements' => $user->achievements()->orderByPivot('earned_at', 'desc')->take(3)->get(),
        ]);
    }

    private function parseDate(?string $value, string $format): ?CarbonImmutable
    {
        if (! $value) {
            return null;
        }

        try {
            $parsed = CarbonImmutable::createFromFormat('!'.$format, $value);
        } catch (InvalidFormatException) {
            return null; // a hand-edited URL must never break the page
        }

        return $parsed && $parsed->format($format) === $value ? $parsed : null;
    }
}
