<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\ContestService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function __invoke(): View
    {
        $competitions = Competition::where('status', '!=', 'draft')
            ->withCount([
                'challenges' => fn ($query) => $query->where('is_published', true),
                'teams',
            ])
            ->orderBy('start_at')
            ->get();

        $live = $competitions->filter->isRunning()->values();
        $upcoming = $competitions->filter->isUpcoming()->values();
        $featured = $live->first();
        $finished = $competitions->filter->isFinished()->sortByDesc('end_at')->values();

        // Fill the last row of the grid with recently finished competitions.
        $cards = $live->concat($upcoming);
        $cards = $cards->concat($finished->take((3 - $cards->count() % 3) % 3));

        return view('home', [
            'live' => $live,
            'upcoming' => $upcoming,
            'cards' => $cards,
            'featured' => $featured,
            'topTeams' => $featured ? $this->contest->ranking($featured)->take(5) : collect(),
            'stats' => $this->contest->globalStats(),
            'activity' => $this->contest->activity(null, null, 8),
        ]);
    }
}
