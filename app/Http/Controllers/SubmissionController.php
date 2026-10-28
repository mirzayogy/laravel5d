<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Submission;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SubmissionController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function store(Request $request, Challenge $challenge): RedirectResponse
    {
        $challenge->load('competition');
        $competition = $challenge->competition;

        abort_unless($challenge->is_published, 404);
        abort_if($competition->status === 'draft', 404);

        $data = $request->validate([
            'flag' => ['required', 'string', 'max:255'],
        ]);

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

        $correct = Hash::check(trim($data['flag']), $challenge->flag_hash);

        Submission::create([
            'team_id' => $team->id,
            'challenge_id' => $challenge->id,
            'user_id' => $request->user()->id,
            'is_correct' => $correct,
            'submitted_at' => now(),
        ]);

        if ($correct) {
            return back()->with('success', 'Correct flag. Your team earned '.$challenge->points.' points.');
        }

        return back()->with('error', 'Wrong flag. Try again.');
    }
}
