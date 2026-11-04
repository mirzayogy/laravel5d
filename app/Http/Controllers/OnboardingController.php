<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function show(Request $request)
    {
        if ($request->user()->profile?->onboarded_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding', [
            'categories' => Category::orderBy('name')->get()->filter(fn ($c) => isset(config('habitude.starters')[$c->slug])),
        ]);
    }

    public function store(Request $request, AchievementService $achievements)
    {
        $data = $request->validate([
            'timezone' => ['required', Rule::in(array_keys(config('habitude.timezones')))],
            'daily_goal' => ['required', 'integer', 'min:1', 'max:20'],
            'starters' => ['array'],
            'starters.*' => ['string', 'regex:/^[a-z\-]+\|\d+$/'],
            'reminder_time' => ['nullable', 'date_format:H:i'],
        ]);

        $user = $request->user();
        $categories = Category::all()->keyBy('slug');

        foreach ($data['starters'] ?? [] as $key) {
            [$slug, $index] = explode('|', $key);
            $starter = config("habitude.starters.{$slug}.{$index}");

            if (! $starter || ! $categories->has($slug)) {
                continue;
            }

            [$name, $target, $unit] = $starter;
            $habit = $user->habits()->create([
                'category_id' => $categories[$slug]->id,
                'name' => __($name), // starter names follow the language they were picked in
                'target_count' => $target,
                'unit' => $unit,
            ]);

            if (! empty($data['reminder_time'])) {
                $habit->reminders()->create([
                    'remind_at' => $data['reminder_time'],
                    'days_of_week' => [1, 2, 3, 4, 5, 6, 7],
                ]);
            }
        }

        $user->profile()->updateOrCreate([], [
            'timezone' => $data['timezone'],
            'daily_goal' => $data['daily_goal'],
            'onboarded_at' => now(),
        ]);

        $achievements->award($user);

        return redirect()->route('dashboard')->with('success', __('You are all set. Check off your first habit!'));
    }
}
