<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Support\Collection;

class AchievementService
{
    public function __construct(private StreakService $streaks) {}

    /**
     * Current value of every criteria type for the user.
     *
     * @return array<string, int>
     */
    public function metrics(User $user): array
    {
        return [
            'logs' => HabitLog::query()
                ->join('habits', 'habits.id', '=', 'habit_logs.habit_id')
                ->where('habits.user_id', $user->id)
                ->whereNull('habits.deleted_at')
                ->whereColumn('habit_logs.value', '>=', 'habits.target_count')
                ->count(),
            'streak' => $this->streaks->overall($user, $user->today())['best'],
            'moods' => $user->moodEntries()->count(),
            'journals' => $user->journalEntries()->count(),
            'habits' => $user->habits()->count(),
        ];
    }

    /**
     * Award every achievement whose criteria is now met.
     *
     * @return Collection<int, Achievement> the newly earned ones
     */
    public function award(User $user): Collection
    {
        $metrics = $this->metrics($user);
        $earnedIds = $user->achievements()->pluck('achievements.id');

        $newly = Achievement::query()
            ->whereNotIn('id', $earnedIds)
            ->get()
            ->filter(function (Achievement $a) use ($metrics) {
                [$type, $threshold] = $a->parsedCriteria();

                return isset($metrics[$type]) && $metrics[$type] >= $threshold;
            })
            ->values();

        if ($newly->isNotEmpty()) {
            $user->achievements()->attach($newly->pluck('id')->mapWithKeys(fn ($id) => [$id => ['earned_at' => now()]])->all());
        }

        return $newly;
    }
}
