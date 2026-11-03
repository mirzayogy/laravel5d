<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Writeup;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function index(Request $request, Competition $competition): View|RedirectResponse
    {
        $user = $request->user();

        abort_if($competition->isDraft() && ! $user->isAdmin(), 404);

        $team = $this->contest->participatingTeam($user, $competition);

        if (! $team && ! $user->isAdmin()) {
            return redirect()
                ->route('competitions.show', $competition)
                ->with('error', 'Your team must be registered to see the challenges.');
        }

        if ($competition->isUpcoming() && ! $user->isAdmin()) {
            return redirect()
                ->route('competitions.show', $competition)
                ->with('status', 'The challenges unlock when the competition starts.');
        }

        $search = trim((string) $request->query('q'));
        $categoryId = $request->query('category');
        $difficulty = $request->query('difficulty');
        $status = $request->query('status');

        $solvedIds = $team ? $this->contest->solvedChallengeIds($team) : [];

        $all = $competition->challenges()->where('is_published', true)->with(['category', 'tags'])->get();

        $challenges = $all
            ->when($search !== '', fn ($items) => $items->filter(fn ($challenge) => Str::contains(Str::lower($challenge->title), Str::lower($search))))
            ->when($categoryId, fn ($items) => $items->where('category_id', (int) $categoryId))
            ->when(in_array($difficulty, ['easy', 'medium', 'hard'], true), fn ($items) => $items->where('difficulty', $difficulty))
            ->when($status === 'solved', fn ($items) => $items->whereIn('id', $solvedIds))
            ->when($status === 'unsolved', fn ($items) => $items->whereNotIn('id', $solvedIds))
            ->sortBy([['category_id', 'asc'], ['points', 'asc']])
            ->values();

        $earned = $all->whereIn('id', $solvedIds)->sum('points');

        return view('challenges.index', [
            'competition' => $competition,
            'team' => $team,
            'challenges' => $challenges,
            'solvedIds' => $solvedIds,
            'solveCounts' => $this->contest->solveCounts($competition),
            'firstBloods' => $this->contest->firstBloods($competition),
            'categories' => Category::orderBy('id')->get(),
            'categoryCounts' => $all->groupBy('category_id')->map->count(),
            'filters' => compact('search', 'categoryId', 'difficulty', 'status'),
            'progress' => [
                'solved' => $all->whereIn('id', $solvedIds)->count(),
                'total' => $all->count(),
                'earned' => $earned,
                'available' => $all->sum('points'),
            ],
            'total' => $all->count(),
        ]);
    }

    public function show(Request $request, Challenge $challenge): View|RedirectResponse
    {
        $user = $request->user();
        $challenge->load(['competition', 'category', 'tags', 'hints']);
        $competition = $challenge->competition;

        abort_unless($challenge->is_published || $user->isAdmin(), 404);
        abort_if($competition->isDraft() && ! $user->isAdmin(), 404);

        $team = $this->contest->participatingTeam($user, $competition);

        if (! $team && ! $user->isAdmin()) {
            return redirect()
                ->route('competitions.show', $competition)
                ->with('error', 'Your team must be registered to open this challenge.');
        }

        if ($competition->isUpcoming() && ! $user->isAdmin()) {
            return redirect()
                ->route('competitions.show', $competition)
                ->with('status', 'The challenges unlock when the competition starts.');
        }

        $solves = Submission::where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->with('team')
            ->orderBy('submitted_at')
            ->get();

        $writeups = Writeup::where('challenge_id', $challenge->id)
            ->where('status', 'published')
            ->with('user')
            ->latest()
            ->limit(4)
            ->get();

        return view('challenges.show', [
            'challenge' => $challenge,
            'competition' => $competition,
            'team' => $team,
            'solved' => $team ? $this->contest->hasSolved($team, $challenge) : false,
            'unlockedHintIds' => $team ? $this->contest->unlockedHintIds($team) : [],
            'failedAttempts' => $team
                ? Submission::where('team_id', $team->id)->where('challenge_id', $challenge->id)->where('is_correct', false)->count()
                : 0,
            'solves' => $solves,
            'writeups' => $writeups,
            'descriptionHtml' => Str::markdown($challenge->description, ['html_input' => 'strip', 'allow_unsafe_links' => false]),
        ]);
    }
}
