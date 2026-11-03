<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Submission;
use App\Notifications\AppNotification;
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
        abort_if($competition->isDraft(), 404);

        $data = $request->validate([
            'flag' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $team = $this->contest->participatingTeam($user, $competition);

        if (! $team) {
            return back()->with('error', 'Your team is not registered in this competition.');
        }

        if (! $competition->isRunning()) {
            return back()->with('error', 'This competition is not running, so submissions are closed.');
        }

        if ($this->contest->hasSolved($team, $challenge)) {
            return back()->with('error', 'Your team already solved this challenge.');
        }

        $correct = Hash::check(trim($data['flag']), $challenge->flag_hash);

        Submission::create([
            'team_id' => $team->id,
            'challenge_id' => $challenge->id,
            'user_id' => $user->id,
            'is_correct' => $correct,
            'submitted_at' => now(),
        ]);

        if (! $correct) {
            return back()->with('error', 'Wrong flag. Take a breath and try again.');
        }

        $firstBlood = Submission::where('challenge_id', $challenge->id)->where('is_correct', true)->count() === 1;

        $team->users()->whereKeyNot($user->id)->get()->each(fn ($mate) => $mate->notify(new AppNotification(
            $firstBlood ? 'First blood!' : 'Your team scored',
            $user->name.' solved '.$challenge->title.' (+'.$challenge->points.' points).',
            route('challenges.show', $challenge),
            $firstBlood ? 'zap' : 'check-circle',
            $firstBlood ? 'coral' : 'green',
        )));

        return back()->with('success', ($firstBlood ? 'First blood! ' : 'Correct flag. ').'Your team earned '.$challenge->points.' points.');
    }
}
