<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $competitions = Competition::withCount(['challenges', 'teams'])
            ->orderByDesc('start_at')
            ->get();

        return view('admin.competitions.index', compact('competitions'));
    }

    public function create(): View
    {
        return view('admin.competitions.form', ['competition' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['title']);

        Competition::create($data);

        return redirect()->route('admin.competitions.index')->with('success', 'Competition created.');
    }

    public function edit(Competition $competition): View
    {
        return view('admin.competitions.form', compact('competition'));
    }

    public function update(Request $request, Competition $competition): RedirectResponse
    {
        $data = $this->validated($request, $competition);
        $data['slug'] = $data['slug'] ?: $competition->slug;

        $competition->update($data);

        return redirect()->route('admin.competitions.index')->with('success', 'Competition updated.');
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();

        return redirect()->route('admin.competitions.index')->with('success', 'Competition deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Competition $competition = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('competitions', 'slug')->ignore($competition?->id),
            ],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'status' => ['required', Rule::in(['draft', 'running', 'finished'])],
        ]);
    }

    private function uniqueSlug(?string $slug, string $title): string
    {
        $base = $slug ?: Str::slug($title);
        $candidate = $base;

        while (Competition::where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.Str::lower(Str::random(4));
        }

        return $candidate;
    }
}
