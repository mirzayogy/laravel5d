<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\ContestService;
use Illuminate\View\View;

class ScoreboardController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function show(Competition $competition): View
    {
        abort_if($competition->status === 'draft', 404);

        $ranking = $this->contest->ranking($competition);

        return view('scoreboard.show', compact('competition', 'ranking'));
    }
}
