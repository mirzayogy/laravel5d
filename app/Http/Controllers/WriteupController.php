<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Writeup;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WriteupController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function index(Request $request): View
    {
        $writeups = Writeup::where('status', 'published')
            ->with(['user', 'challenge'])
            ->latest()
            ->paginate(10);

        $mine = $request->user()
            ? $request->user()->writeups()->with('challenge')->latest()->get()
            : collect();

        return view('writeups.index', compact('writeups', 'mine'));
    }

    public function show(Request $request, Writeup $writeup): View
    {
        $isAuthor = $request->user()?->id === $writeup->user_id;

        abort_unless($writeup->status === 'published' || $isAuthor, 404);

        $writeup->load(['user', 'challenge']);

        return view('writeups.show', compact('writeup', 'isAuthor'));
    }

    public function create(Request $request, Challenge $challenge): View|RedirectResponse
    {
        if (! $this->canWrite($request, $challenge)) {
            return redirect()->route('writeups.index')
                ->with('error', 'Your team must solve this challenge before you can write about it.');
        }

        return view('writeups.form', ['writeup' => null, 'challenge' => $challenge]);
    }

    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        if (! $this->canWrite($request, $challenge)) {
            return redirect()->route('writeups.index')
                ->with('error', 'Your team must solve this challenge before you can write about it.');
        }

        $data = $this->validated($request);

        $writeup = Writeup::create($data + [
            'user_id' => $request->user()->id,
            'challenge_id' => $challenge->id,
        ]);

        return redirect()->route('writeups.show', $writeup)->with('success', 'Writeup saved.');
    }

    public function edit(Request $request, Writeup $writeup): View
    {
        abort_unless($writeup->user_id === $request->user()->id, 403);

        $challenge = $writeup->challenge;

        return view('writeups.form', compact('writeup', 'challenge'));
    }

    public function update(Request $request, Writeup $writeup): RedirectResponse
    {
        abort_unless($writeup->user_id === $request->user()->id, 403);

        $writeup->update($this->validated($request));

        return redirect()->route('writeups.show', $writeup)->with('success', 'Writeup updated.');
    }

    public function destroy(Request $request, Writeup $writeup): RedirectResponse
    {
        abort_unless($writeup->user_id === $request->user()->id, 403);

        $writeup->delete();

        return redirect()->route('writeups.index')->with('success', 'Writeup deleted.');
    }

    private function canWrite(Request $request, Challenge $challenge): bool
    {
        $team = $request->user()->team;

        return $team !== null && $this->contest->hasSolved($team, $challenge);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);
    }
}
