<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AchievementController extends Controller
{
    public function index(Request $request, AchievementService $service)
    {
        $user = $request->user();
        $service->award($user);

        return view('achievements.index', [
            'admin' => request()->routeIs('admin.*'),
            'achievements' => Achievement::orderBy('name')->get(),
            'earned' => $user->achievements()->get()->keyBy('id'),
            'metrics' => $service->metrics($user),
        ]);
    }

    public function create()
    {
        return view('achievements.create');
    }

    public function store(Request $request, AchievementService $service)
    {
        Achievement::create($this->validated($request));
        $service->award($request->user());

        return redirect()->route('admin.achievements.index')->with('success', __('Achievement created.'));
    }

    public function edit(Achievement $achievement)
    {
        [$type, $threshold] = $achievement->parsedCriteria();

        return view('achievements.edit', compact('achievement', 'type', 'threshold'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $achievement->update($this->validated($request, $achievement));

        return redirect()->route('admin.achievements.index')->with('success', __('Achievement updated.'));
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete(); // achievement_user rows go with it (cascade)

        return redirect()->route('admin.achievements.index')->with('success', __('Achievement deleted.'));
    }

    private function validated(Request $request, ?Achievement $achievement = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('achievements', 'name')->ignore($achievement)],
            'description' => ['nullable', 'string', 'max:255'],
            'criteria_type' => ['required', Rule::in(array_keys(config('habitude.criteria')))],
            'criteria_threshold' => ['required', 'integer', 'min:1', 'max:100000'],
        ], [
            'name.unique' => __('This achievement name is already used.'),
            'criteria_type.required' => __('Choose what unlocks it.'),
            'criteria_threshold.required' => __('Set how many are needed.'),
        ]);

        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'criteria' => $data['criteria_type'].':'.$data['criteria_threshold'],
        ];
    }
}
