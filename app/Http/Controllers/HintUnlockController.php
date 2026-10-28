<?php

namespace App\Http\Controllers;

use App\Models\Hint;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HintUnlockController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function store(Request $request, Hint $hint): RedirectResponse
    {
        $hint->load('challenge.competition');
        $challenge = $hint->challenge;
        $competition = $challenge->competition;

        abort_unless($challenge->is_published, 404);
        abort_if($competition->status === 'draft', 404);

        $team = $this->contest->participatingTeam($request->user(), $competition);

        if (! $team) {
            return back()->with('error', 'Your team is not registered in this competition.');
        }

        if ($competition->status !== 'running') {
            return back()->with('error', 'This competition is not running.');
        }

        if ($this->contest->hasSolved($team, $challenge)) {
            return back()->with('error', 'Your team already solved this challenge.');
        }

        if (in_array($hint->id, $this->contest->unlockedHintIds($team), true)) {
            return back()->with('error', 'This hint is already unlocked.');
        }

        $team->hints()->attach($hint->id, ['unlocked_at' => now()]);

        return back()->with('success', 'Hint unlocked. This costs your team '.$hint->cost.' points.');
    }
}
