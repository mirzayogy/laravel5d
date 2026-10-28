<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function index(): View
    {
        $challenges = Challenge::with(['competition', 'category'])
            ->latest()
            ->paginate(15);

        return view('admin.challenges.index', compact('challenges'));
    }

    public function create(): View
    {
        return view('admin.challenges.form', $this->formData(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);

        $challenge = Challenge::create([
            'competition_id' => $data['competition_id'],
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'points' => $data['points'],
            'flag_hash' => Hash::make($data['flag']),
            'difficulty' => $data['difficulty'],
            'is_published' => $request->boolean('is_published'),
        ]);

        $this->syncTags($challenge, $request);

        return redirect()->route('admin.challenges.edit', $challenge)->with('success', 'Challenge created.');
    }

    public function edit(Challenge $challenge): View
    {
        $challenge->load(['tags', 'hints']);

        return view('admin.challenges.form', $this->formData($challenge));
    }

    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $data = $this->validated($request, false);

        $attributes = [
            'competition_id' => $data['competition_id'],
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'points' => $data['points'],
            'difficulty' => $data['difficulty'],
            'is_published' => $request->boolean('is_published'),
        ];

        if (! empty($data['flag'])) {
            $attributes['flag_hash'] = Hash::make($data['flag']);
        }

        $challenge->update($attributes);
        $this->syncTags($challenge, $request);

        return redirect()->route('admin.challenges.edit', $challenge)->with('success', 'Challenge updated.');
    }

    public function destroy(Challenge $challenge): RedirectResponse
    {
        $challenge->delete();

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, bool $flagRequired): array
    {
        return $request->validate([
            'competition_id' => ['required', 'exists:competitions,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:10000'],
            'flag' => [$flagRequired ? 'required' : 'nullable', 'string', 'max:255'],
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'is_published' => ['nullable', 'boolean'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'new_tags' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncTags(Challenge $challenge, Request $request): void
    {
        $ids = array_map('intval', $request->input('tag_ids', []));

        $names = collect(explode(',', (string) $request->input('new_tags')))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique();

        foreach ($names as $name) {
            $ids[] = Tag::firstOrCreate(['name' => $name])->id;
        }

        $challenge->tags()->sync(array_unique($ids));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Challenge $challenge): array
    {
        return [
            'challenge' => $challenge,
            'competitions' => Competition::orderBy('title')->get(),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }
}
