<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Models\Writeup;
use App\Services\ContestService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private ContestService $contest) {}

    public function __invoke(): View
    {
        $since = now()->subDays(13)->startOfDay();

        $perDay = Submission::where('submitted_at', '>=', $since)
            ->select(DB::raw('date(submitted_at) as day'), DB::raw('count(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = collect(range(13, 0))->map(fn (int $ago) => now()->subDays($ago)->startOfDay());

        $competitions = Competition::withCount(['challenges', 'teams'])->orderByDesc('start_at')->get();

        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'teams' => Team::count(),
                'competitions' => $competitions->count(),
                'live' => $competitions->filter->isRunning()->count(),
                'challenges' => Challenge::count(),
                'published' => Challenge::where('is_published', true)->count(),
                'attempts' => Submission::count(),
                'solves' => Submission::where('is_correct', true)->count(),
                'writeups' => Writeup::where('status', 'published')->count(),
            ],
            'labels' => $days->map->format('d M')->all(),
            'values' => $days->map(fn ($day) => (int) ($perDay[$day->toDateString()] ?? 0))->all(),
            'competitions' => $competitions->take(5),
            'latestUsers' => User::with('team')->latest()->limit(6)->get(),
            'topTeams' => Team::withCount(['users', 'submissions as solves_count' => fn ($query) => $query->where('is_correct', true)])
                ->orderByDesc('solves_count')
                ->limit(5)
                ->get(),
            'activity' => $this->contest->activity(null, null, 8),
        ]);
    }
}
