<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Submission;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function index(Request $request, Competition $competition): View|RedirectResponse
    {
        abort_if($competition->status === 'draft', 404);

        $team = $this->contest->participatingTeam($request->user(), $competition);

        if (! $team) {
            return redirect()
                ->route('competitions.show', $competition)
                ->with('error', 'Your team must be registered to see the challenges.');
        }

        $challenges = $competition->challenges()
            ->where('is_published', true)
            ->with('category')
            ->orderBy('category_id')
            ->orderBy('points')
            ->get();

        $solvedIds = $this->contest->solvedChallengeIds($team);

        return view('challenges.index', compact('competition', 'challenges', 'solvedIds'));
    }

    public function show(Request $request, Challenge $challenge): View|RedirectResponse
    {
        $challenge->load(['competition', 'category', 'tags', 'hints']);
        $competition = $challenge->competition;

        abort_unless($challenge->is_published, 404);
        abort_if($competition->status === 'draft', 404);

        $team = $this->contest->participatingTeam($request->user(), $competition);

        if (! $team) {
            return redirect()
                ->route('competitions.show', $competition)
                ->with('error', 'Your team must be registered to open this challenge.');
        }

        $solved = $this->contest->hasSolved($team, $challenge);
        $unlockedHintIds = $this->contest->unlockedHintIds($team);
        $failedAttempts = Submission::where('team_id', $team->id)
            ->where('challenge_id', $challenge->id)
            ->where('is_correct', false)
            ->count();

        return view('challenges.show', compact(
            'challenge',
            'competition',
            'solved',
            'unlockedHintIds',
            'failedAttempts',
        ));
    }
}
