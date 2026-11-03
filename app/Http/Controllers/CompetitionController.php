<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Notifications\AppNotification;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $phase = $request->query('phase');

        $competitions = Competition::where('status', '!=', 'draft')
            ->withCount([
                'challenges' => fn ($query) => $query->where('is_published', true),
                'teams',
            ])
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->orderByDesc('start_at')
            ->get();

        $counts = [
            'all' => $competitions->count(),
            'running' => $competitions->filter->isRunning()->count(),
            'upcoming' => $competitions->filter->isUpcoming()->count(),
            'finished' => $competitions->filter->isFinished()->count(),
        ];

        if (in_array($phase, ['running', 'upcoming', 'finished'], true)) {
            $competitions = $competitions->filter(fn (Competition $competition) => $competition->phase === $phase);
        }

        // Live first, then upcoming (soonest first), then finished (latest first).
        $order = ['running' => 0, 'upcoming' => 1, 'finished' => 2];
        $competitions = $competitions->sortBy([
            fn ($a, $b) => $order[$a->phase] <=> $order[$b->phase],
            fn ($a, $b) => $a->phase === 'upcoming' ? $a->start_at <=> $b->start_at : $b->start_at <=> $a->start_at,
        ])->values();

        $team = $request->user()?->team;
        $registeredIds = $team ? $team->competitions()->pluck('competitions.id')->all() : [];

        return view('competitions.index', compact('competitions', 'counts', 'phase', 'search', 'registeredIds'));
    }

    public function show(Request $request, Competition $competition): View
    {
        $user = $request->user();

        abort_if($competition->isDraft() && ! $user?->isAdmin(), 404);

        $team = $user?->team;
        $participating = $this->contest->participatingTeam($user, $competition) !== null;

        $challenges = $competition->challenges()->where('is_published', true)->with('category')->get();
        $solvedIds = $participating ? $this->contest->solvedChallengeIds($team) : [];

        $categories = $challenges->groupBy(fn ($challenge) => $challenge->category->name)
            ->map(fn ($items) => [
                'total' => $items->count(),
                'solved' => $items->whereIn('id', $solvedIds)->count(),
            ])
            ->sortKeys();

        $ranking = $this->contest->ranking($competition);

        return view('competitions.show', [
            'competition' => $competition,
            'team' => $team,
            'participating' => $participating,
            'stats' => $this->contest->stats($competition),
            'categories' => $categories,
            'topTeams' => $ranking->take(5),
            'difficulty' => $challenges->countBy('difficulty'),
            'bloods' => $this->contest->firstBloods($competition)->sortByDesc('at')->take(4),
            'titles' => $challenges->pluck('title', 'id'),
            'myStanding' => $participating ? $this->contest->standing($team, $competition) : null,
            'activity' => $this->contest->activity($competition, null, 8),
        ]);
    }

    public function register(Request $request, Competition $competition): RedirectResponse
    {
        abort_if($competition->isDraft(), 404);

        $user = $request->user();
        $team = $user->team;

        if (! $team) {
            return back()->with('error', 'Create or join a team before registering.');
        }

        if ($competition->isFinished()) {
            return back()->with('error', 'This competition has ended, so registration is closed.');
        }

        if ($this->contest->participatingTeam($user, $competition)) {
            return back()->with('error', 'Your team is already registered.');
        }

        $team->competitions()->attach($competition->id, ['registered_at' => now()]);

        $team->users()->whereKeyNot($user->id)->get()->each(fn ($mate) => $mate->notify(new AppNotification(
            'Team registered',
            $user->name.' registered '.$team->name.' for '.$competition->title.'.',
            route('competitions.show', $competition),
            'flag',
            'sky',
        )));

        return back()->with('success', $team->name.' is registered. Good luck!');
    }
}
