<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Writeup;
use App\Services\ContestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WriteupController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $competitionId = $request->query('competition');
        $mine = $request->boolean('mine') && $request->user();

        $writeups = Writeup::query()
            ->with(['user', 'challenge.category', 'challenge.competition'])
            ->when($mine, fn ($query) => $query->where('user_id', $request->user()->id))
            ->when(! $mine, fn ($query) => $query->where('status', 'published'))
            ->when($search !== '', fn ($query) => $query->where(function ($inner) use ($search) {
                $inner->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('challenge', fn ($challenge) => $challenge->where('title', 'like', '%'.$search.'%'));
            }))
            ->when($competitionId, fn ($query) => $query->whereHas('challenge', fn ($challenge) => $challenge->where('competition_id', (int) $competitionId)))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $writeups->getCollection()->each(function (Writeup $writeup) {
            $writeup->excerpt = Str::limit(html_entity_decode(strip_tags(Str::markdown($writeup->content, ['html_input' => 'strip'])), ENT_QUOTES), 140);
        });

        return view('writeups.index', [
            'writeups' => $writeups,
            'search' => $search,
            'competitionId' => $competitionId,
            'mine' => $mine,
            'competitions' => Competition::where('status', '!=', 'draft')->orderByDesc('start_at')->get(),
            'myCount' => $request->user() ? $request->user()->writeups()->count() : 0,
            'total' => Writeup::where('status', 'published')->count(),
        ]);
    }

    public function show(Request $request, Writeup $writeup): View
    {
        $isAuthor = $request->user()?->id === $writeup->user_id;

        abort_unless($writeup->status === 'published' || $isAuthor, 404);

        $writeup->load(['user.profile', 'challenge.category', 'challenge.competition']);

        return view('writeups.show', [
            'writeup' => $writeup,
            'isAuthor' => $isAuthor,
            'html' => Str::markdown($writeup->content, ['html_input' => 'strip', 'allow_unsafe_links' => false]),
            'more' => Writeup::where('challenge_id', $writeup->challenge_id)
                ->where('status', 'published')
                ->whereKeyNot($writeup->id)
                ->with('user')
                ->latest()
                ->limit(4)
                ->get(),
        ]);
    }

    public function create(Request $request, Challenge $challenge): View|RedirectResponse
    {
        if (! $this->canWrite($request, $challenge)) {
            return redirect()->route('writeups.index')
                ->with('error', 'Your team must solve this challenge before you can write about it.');
        }

        return view('writeups.form', ['writeup' => null, 'challenge' => $challenge->load('category', 'competition')]);
    }

    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        if (! $this->canWrite($request, $challenge)) {
            return redirect()->route('writeups.index')
                ->with('error', 'Your team must solve this challenge before you can write about it.');
        }

        $writeup = Writeup::create($this->validated($request) + [
            'user_id' => $request->user()->id,
            'challenge_id' => $challenge->id,
        ]);

        return redirect()->route('writeups.show', $writeup)->with('success', 'Writeup saved.');
    }

    public function edit(Request $request, Writeup $writeup): View
    {
        abort_unless($writeup->user_id === $request->user()->id, 403);

        return view('writeups.form', ['writeup' => $writeup, 'challenge' => $writeup->challenge->load('category', 'competition')]);
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

    /** Renders Markdown for the live preview in the writeup form. */
    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate(['content' => ['nullable', 'string', 'max:20000']]);

        return response()->json([
            'html' => Str::markdown($data['content'] ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]),
        ]);
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
            'content' => ['required', 'string', 'max:20000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);
    }
}
