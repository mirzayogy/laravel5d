<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\ContestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreboardController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function show(Request $request, Competition $competition): View
    {
        abort_if($competition->isDraft() && ! $request->user()?->isAdmin(), 404);

        $ranking = $this->contest->ranking($competition);

        return view('scoreboard.show', [
            'competition' => $competition,
            'ranking' => $ranking,
            'series' => $this->contest->series($competition),
            'stats' => $this->contest->stats($competition),
            'myTeamId' => $request->user()?->team_id,
        ]);
    }

    public function data(Request $request, Competition $competition): JsonResponse
    {
        abort_if($competition->isDraft() && ! $request->user()?->isAdmin(), 404);

        return response()->json([
            'phase' => $competition->phase,
            'ranking' => $this->contest->rankingRows($competition),
            'series' => $this->contest->series($competition),
        ]);
    }
}
