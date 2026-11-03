<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\ContestService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $team = $user->team?->load('users', 'captain');

        $registered = $team
            ? $team->competitions()->where('status', '!=', 'draft')->orderByDesc('start_at')->get()
            : collect();

        // The competition we show numbers for: a running one first, then an upcoming one, then the latest.
        $current = $registered->first(fn (Competition $competition) => $competition->isRunning())
            ?? $registered->first(fn (Competition $competition) => $competition->isUpcoming())
            ?? $registered->first();

        $standing = null;
        $progress = null;

        if ($team && $current) {
            $standing = $current->isUpcoming() ? null : $this->contest->standing($team, $current);
            $total = $current->challenges()->where('is_published', true)->count();
            $solved = count(array_intersect(
                $this->contest->solvedChallengeIds($team),
                $current->challenges()->where('is_published', true)->pluck('id')->all(),
            ));
            $progress = ['solved' => $solved, 'total' => $total];
        }

        $open = Competition::where('status', '!=', 'draft')
            ->withCount(['challenges' => fn ($query) => $query->where('is_published', true), 'teams'])
            ->orderBy('start_at')
            ->get()
            ->filter(fn (Competition $competition) => ! $competition->isFinished() && ! $registered->contains('id', $competition->id))
            ->values();

        return view('dashboard', [
            'user' => $user,
            'team' => $team,
            'registered' => $registered,
            'current' => $current,
            'standing' => $standing,
            'progress' => $progress,
            'open' => $open,
            'activity' => $team ? $this->contest->activity(null, $team, 8) : collect(),
            'notifications' => $user->notifications()->latest()->limit(4)->get(),
            'writeups' => $user->writeups()->with('challenge')->latest()->limit(3)->get(),
        ]);
    }
}
