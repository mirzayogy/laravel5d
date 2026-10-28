<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContestService
{
    /**
     * The team of the user, but only when that team is registered in the competition.
     */
    public function participatingTeam(?User $user, Competition $competition): ?Team
    {
        $team = $user?->team;

        if (! $team) {
            return null;
        }

        $registered = $team->competitions()->whereKey($competition->id)->exists();

        return $registered ? $team : null;
    }

    public function hasSolved(Team $team, Challenge $challenge): bool
    {
        return Submission::where('team_id', $team->id)
            ->where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->exists();
    }

    /**
     * @return list<int>
     */
    public function solvedChallengeIds(Team $team): array
    {
        return Submission::where('team_id', $team->id)
            ->where('is_correct', true)
            ->pluck('challenge_id')
            ->all();
    }

    /**
     * @return list<int>
     */
    public function unlockedHintIds(Team $team): array
    {
        return DB::table('hint_unlocks')
            ->where('team_id', $team->id)
            ->pluck('hint_id')
            ->all();
    }

    /**
     * Ranking of the teams registered in a competition.
     *
     * Score = points of solved challenges - cost of unlocked hints.
     * Ties are broken by the earlier last solve, then by team name.
     *
     * @return Collection<int, object>
     */
    public function ranking(Competition $competition)
    {
        $solved = Submission::query()
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->where('challenges.competition_id', $competition->id)
            ->where('submissions.is_correct', true)
            ->selectRaw('submissions.team_id as team_id, SUM(challenges.points) as points, COUNT(*) as solves, MAX(submissions.submitted_at) as last_solved_at')
            ->groupBy('submissions.team_id')
            ->get()
            ->keyBy('team_id');

        $costs = DB::table('hint_unlocks')
            ->join('hints', 'hints.id', '=', 'hint_unlocks.hint_id')
            ->join('challenges', 'challenges.id', '=', 'hints.challenge_id')
            ->where('challenges.competition_id', $competition->id)
            ->selectRaw('hint_unlocks.team_id as team_id, SUM(hints.cost) as cost')
            ->groupBy('hint_unlocks.team_id')
            ->pluck('cost', 'team_id');

        return $competition->teams()->get()
            ->map(function (Team $team) use ($solved, $costs) {
                $row = $solved->get($team->id);
                $points = (int) ($row->points ?? 0);
                $hintCost = (int) ($costs[$team->id] ?? 0);

                return (object) [
                    'team' => $team,
                    'solves' => (int) ($row->solves ?? 0),
                    'points' => $points,
                    'hint_cost' => $hintCost,
                    'score' => $points - $hintCost,
                    'last_solved_at' => $row->last_solved_at ?? null,
                ];
            })
            ->sort(fn ($a, $b) => [$b->score, $a->last_solved_at ?? 'z', $a->team->name]
                <=> [$a->score, $b->last_solved_at ?? 'z', $b->team->name])
            ->values();
    }
}
