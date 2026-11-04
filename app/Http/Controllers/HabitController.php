<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Services\AchievementService;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitController extends Controller
{
    public function index(Request $request, StreakService $streaks)
    {
        $user = $request->user();
        $today = $user->today();

        $group = in_array($request->query('group'), ['category', 'status', 'none'], true) ? $request->query('group') : 'category';
        $view = $request->query('view') === 'grid' ? 'grid' : 'list';

        $habits = $user->habits()->with(['category', 'tags'])->orderBy('name')->get()
            ->each(fn ($h) => $h->streak = $streaks->habitStreak($h, $today));

        $groups = match ($group) {
            'category' => $habits->groupBy(fn ($h) => $h->category->name)->sortKeys(),
            'status' => $habits->groupBy(fn ($h) => $h->is_active ? 'Active' : 'Paused'),
            default => collect(['All habits' => $habits]),
        };

        return view('habits.index', compact('groups', 'group', 'view', 'habits'));
    }

    public function create()
    {
        return view('habits.create', $this->formData());
    }

    public function store(Request $request, AchievementService $achievements)
    {
        $data = $this->validated($request);

        $habit = $request->user()->habits()->create($data);
        $habit->tags()->sync($request->input('tags', []));
        $achievements->award($request->user());

        // Quick add (just a name and a category) stays where you were; the full form goes on to reminders.
        if ($request->boolean('quick')) {
            return redirect($this->safeBack($request))->with('success', __('":name" added.', ['name' => $habit->name]));
        }

        return redirect()->route('habits.edit', $habit)
            ->with('success', __('Habit created. You can add reminders below.'));
    }

    public function edit(Request $request, Habit $habit)
    {
        $this->authorizeOwner($request, $habit);

        return view('habits.edit', ['habit' => $habit->load(['tags', 'reminders'])] + $this->formData());
    }

    public function update(Request $request, Habit $habit)
    {
        $this->authorizeOwner($request, $habit);

        $habit->update($this->validated($request));
        $habit->tags()->sync($request->input('tags', []));

        return redirect()->route('habits.index')
            ->with('success', __('Habit updated.'));
    }

    public function destroy(Request $request, Habit $habit)
    {
        $this->authorizeOwner($request, $habit);

        $habit->delete(); // soft delete: it can be restored for 30 days

        return redirect()->route('habits.index')
            ->with('success', __('":name" deleted.', ['name' => $habit->name]))
            ->with('undo', route('habits.restore', $habit->id));
    }

    public function restore(Request $request, int $habit)
    {
        $habit = $request->user()->habits()->onlyTrashed()->findOrFail($habit);
        $habit->restore();

        if ($request->expectsJson()) {
            return response()->json(['restored' => $habit->id]);
        }

        return redirect()->route('habits.index')->with('success', __('":name" restored.', ['name' => $habit->name]));
    }

    /** Only ever send people back to a page of this app. */
    private function safeBack(Request $request): string
    {
        $back = (string) $request->input('back');

        return str_starts_with($back, url('/')) ? $back : route('habits.index');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'units' => config('habitude.units'),
        ];
    }

    private function authorizeOwner(Request $request, Habit $habit): void
    {
        abort_unless($habit->user_id === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'target_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'unit' => ['nullable', 'string', Rule::in(config('habitude.units'))],
            'weekly_goal' => ['nullable', 'integer', 'between:0,6'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ], [
            'category_id.required' => __('Choose a category.'),
            'name.required' => __('Habit name is required.'),
            'target_count.min' => __('The target must be at least 1.'),
            'unit.in' => __('Choose one of the listed units.'),
            'weekly_goal.between' => __('Choose every day, or 1 to 6 days a week.'),
        ]);

        // Quick add sends only a name and a category: fill the rest from what suits that category.
        $default = config('habitude.defaults.'.Category::find($data['category_id'])?->slug) ?? ['target' => 1, 'unit' => 'times'];
        $data['target_count'] ??= $default['target'];
        $data['unit'] ??= $default['unit'];

        // 0 (or nothing) means every day; 1 to 6 is a weekly goal.
        $data['weekly_goal'] = ((int) ($data['weekly_goal'] ?? 0)) ?: null;

        // An unchecked switch is not submitted, so read it as a boolean (quick add always starts active).
        $data['is_active'] = $request->boolean('quick') ? true : $request->boolean('is_active');
        unset($data['tags']);

        return $data;
    }
}
