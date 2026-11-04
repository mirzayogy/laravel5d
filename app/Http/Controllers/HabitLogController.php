<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Services\AchievementService;
use App\Services\StreakService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class HabitLogController extends Controller
{
    /**
     * Check a habit in for a day. Always answers JSON, because the dashboard
     * updates in place (spring check, counters, calendar).
     *
     * mode "toggle": complete the day, or clear it if already complete
     * mode "add":    add `value` to today's progress (also used by the timer)
     * mode "set":    set progress to `value` (0 clears the log)
     */
    public function store(Request $request, Habit $habit, StreakService $streaks, AchievementService $achievements)
    {
        abort_unless($habit->user_id === $request->user()->id, 404);

        $today = $request->user()->today();

        $data = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:'.$today->toDateString()],
            'mode' => ['required', 'in:toggle,add,set'],
            'value' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $date = CarbonImmutable::parse($data['date'] ?? $today->toDateString())->toDateString();
        $log = $habit->logs()->whereDate('logged_date', $date)->first();
        $current = $log->value ?? 0;

        $value = match ($data['mode']) {
            'toggle' => $current >= $habit->target_count ? 0 : $habit->target_count,
            'add' => $current + max(1, (int) ($data['value'] ?? 1)),
            'set' => (int) ($data['value'] ?? 0),
        };

        if ($value <= 0) {
            $log?->delete();
            $value = 0;
        } elseif ($log) {
            $log->update(['value' => $value]);
        } else {
            $habit->logs()->create(['logged_date' => $date, 'value' => $value]);
        }

        $user = $request->user();
        $earned = $achievements->award($user);

        $habits = $user->habits()->where('is_active', true)->get(['id', 'target_count']);
        $doneToday = $habit->logs()->getModel()->newQuery()
            ->whereIn('habit_id', $habits->pluck('id'))
            ->whereDate('logged_date', $date)
            ->get()
            ->filter(fn ($l) => $l->value >= $habits->firstWhere('id', $l->habit_id)->target_count)
            ->count();

        return response()->json([
            'date' => $date,
            'value' => $value,
            'target' => $habit->target_count,
            'completed' => $value >= $habit->target_count,
            'habit_streak' => $streaks->habitStreak($habit, $today),
            'week_done' => $habit->isWeekly() ? ($streaks->weekCounts(collect([$habit]), CarbonImmutable::parse($date))[$habit->id] ?? 0) : null,
            'streak' => $streaks->overall($user, $today),
            'done' => $doneToday,
            'total' => $habits->count(),
            'earned' => $earned->map(fn ($a) => ['name' => $a->display_name, 'description' => $a->display_description])->all(),
        ]);
    }
}
