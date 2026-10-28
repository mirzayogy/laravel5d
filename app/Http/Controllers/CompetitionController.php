<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function index(): View
    {
        $competitions = Competition::whereIn('status', ['running', 'finished'])
            ->withCount([
                'challenges' => fn ($query) => $query->where('is_published', true),
                'teams',
            ])
            ->orderByDesc('start_at')
            ->get();

        return view('competitions.index', compact('competitions'));
    }

    public function show(Request $request, Competition $competition): View
    {
        abort_if($competition->status === 'draft', 404);

        $user = $request->user();
        $team = $user?->team;
        $registered = $this->contest->participatingTeam($user, $competition) !== null;

        $competition->loadCount([
            'challenges' => fn ($query) => $query->where('is_published', true),
            'teams',
        ]);

        return view('competitions.show', compact('competition', 'team', 'registered'));
    }

    public function register(Request $request, Competition $competition): RedirectResponse
    {
        abort_if($competition->status === 'draft', 404);

        $team = $request->user()->team;

        if (! $team) {
            return back()->with('error', 'Create or join a team before registering.');
        }

        if ($competition->status !== 'running') {
            return back()->with('error', 'Registration is only open while the competition is running.');
        }

        if ($this->contest->participatingTeam($request->user(), $competition)) {
            return back()->with('error', 'Your team is already registered.');
        }

        $team->competitions()->attach($competition->id, ['registered_at' => now()]);

        return back()->with('success', 'Your team is registered.');
    }
}
