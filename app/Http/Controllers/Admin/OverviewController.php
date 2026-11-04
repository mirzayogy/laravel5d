<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\JournalEntry;
use App\Models\MoodEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    public function index()
    {
        $days = collect(range(13, 0))->map(fn ($i) => CarbonImmutable::now()->subDays($i)->startOfDay());

        $signups = User::where('created_at', '>=', $days->first())->get(['created_at'])
            ->groupBy(fn ($u) => $u->created_at->toDateString());

        // Has-Many-Through across every user: Category -> Habit -> HabitLog
        $topCategories = Category::query()
            ->withCount(['habitLogs as completed' => fn ($q) => $q->whereColumn('habit_logs.value', '>=', 'habits.target_count')])
            ->get()->filter(fn ($c) => $c->completed > 0)->sortByDesc('completed')->take(5)->values();

        return view('admin.overview', [
            'stats' => [
                'Users' => User::count(),
                'Habits' => Habit::count(),
                'Check-ins' => HabitLog::whereHas('habit')->count(),
                'Moods' => MoodEntry::count(),
                'Journals' => JournalEntry::count(),
                'Unlocked' => DB::table('achievement_user')->count(),
            ],
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'signups' => $days->map(fn ($d) => ['date' => $d, 'count' => ($signups[$d->toDateString()] ?? collect())->count()]),
            'topCategories' => $topCategories,
            'recentUsers' => User::latest()->limit(5)->get(),
            'system' => [
                'Release stage' => ucfirst(config('habitude.stage')),
                'Environment' => app()->environment(),
                'Laravel' => app()->version(),
                'PHP' => PHP_VERSION,
                'Database' => DB::connection()->getDriverName(),
                'Timezone' => config('app.timezone'),
            ],
        ]);
    }
}
