<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Reminder;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    /**
     * What is still to do today, for the browser notifier: enabled reminders of
     * active habits that are not finished yet, with the time in the user's own clock.
     */
    public function today(Request $request)
    {
        $user = $request->user();
        $today = $user->today();
        $weekday = $today->isoWeekday();
        $now = CarbonImmutable::now($user->profile?->timezone ?? config('app.timezone'));

        $habits = $user->habits()->where('is_active', true)->with('reminders')->get();
        $done = HabitLog::whereIn('habit_id', $habits->pluck('id'))->whereDate('logged_date', $today->toDateString())->get()->keyBy('habit_id');

        $items = $habits->flatMap(fn ($h) => $h->reminders
            ->filter(fn ($r) => $r->is_enabled && in_array($weekday, $r->days_of_week ?? [], true))
            ->reject(fn () => ($done[$h->id]->value ?? 0) >= $h->target_count)
            ->map(fn ($r) => ['id' => $r->id, 'habit' => $h->name, 'detail' => $h->target_label, 'time' => substr($r->remind_at, 0, 5)]))
            ->sortBy('time')->values();

        return response()->json(['now' => $now->format('H:i'), 'date' => $today->toDateString(), 'items' => $items]);
    }

    public function store(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'remind_at' => ['required', 'date_format:H:i'],
            'days_of_week' => ['required', 'array', 'min:1'],
            'days_of_week.*' => ['integer', 'between:1,7'],
        ], [
            'remind_at.required' => __('Pick a time.'),
            'days_of_week.required' => __('Pick at least one day.'),
        ]);

        $habit->reminders()->create([
            'remind_at' => $data['remind_at'],
            'days_of_week' => collect($data['days_of_week'])->map(fn ($d) => (int) $d)->unique()->sort()->values()->all(),
        ]);

        return redirect()->route('habits.edit', $habit)->with('success', __('Reminder added.'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->habit->user_id === $request->user()->id, 404);

        $reminder->update(['is_enabled' => $request->boolean('is_enabled')]);

        if ($request->expectsJson()) {
            return response()->json(['is_enabled' => $reminder->is_enabled]);
        }

        return redirect()->route('habits.edit', $reminder->habit_id)->with('success', __('Reminder updated.'));
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->habit->user_id === $request->user()->id, 404);

        $habitId = $reminder->habit_id;
        $reminder->delete();

        return redirect()->route('habits.edit', $habitId)->with('success', __('Reminder deleted.'));
    }
}
