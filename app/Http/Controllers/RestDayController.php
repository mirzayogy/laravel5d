<?php

namespace App\Http\Controllers;

use App\Services\StreakService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class RestDayController extends Controller
{
    /**
     * Take a rest day for today or yesterday: one per week (Monday to Sunday), and only
     * on a day with no completed habit, so it protects the streak instead of padding it.
     */
    public function store(Request $request, StreakService $streaks)
    {
        $user = $request->user();
        $today = $user->today();

        $data = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:'.$today->subDay()->toDateString(), 'before_or_equal:'.$today->toDateString()],
        ]);

        $date = CarbonImmutable::parse($data['date'] ?? $today->toDateString());

        if ($user->restDayInWeekOf($date)) {
            return back()->with('error', __('You already took a rest day that week.'));
        }

        if ($streaks->completedDates($user)->contains($date->toDateString())) {
            return back()->with('error', __('You already completed a habit that day, so it does not need a rest.'));
        }

        $user->restDays()->create(['rest_date' => $date->toDateString()]);

        return back()->with('success', __('Rest day set. Your streak is safe.'));
    }

    public function destroy(Request $request, string $date)
    {
        $request->user()->restDays()->whereDate('rest_date', $date)->delete();

        return back()->with('success', __('Rest day removed.'));
    }
}
