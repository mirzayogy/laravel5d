<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Models\Writeup;
use Illuminate\Support\Carbon;
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
    public function ranking(Competition $competition): Collection
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

    /**
     * The position and score of one team in a competition, or null when it is not registered.
     *
     * @return array{rank: int, row: object, total: int}|null
     */
    public function standing(Team $team, Competition $competition): ?array
    {
        $ranking = $this->ranking($competition);
        $index = $ranking->search(fn ($row) => $row->team->id === $team->id);

        if ($index === false) {
            return null;
        }

        return ['rank' => $index + 1, 'row' => $ranking[$index], 'total' => $ranking->count()];
    }

    /**
     * The ranking as plain arrays, for the scoreboard JSON endpoint.
     *
     * @return list<array<string, mixed>>
     */
    public function rankingRows(Competition $competition): array
    {
        return $this->ranking($competition)->map(fn ($row) => [
            'team_id' => $row->team->id,
            'name' => $row->team->name,
            'solves' => $row->solves,
            'points' => $row->points,
            'hint_cost' => $row->hint_cost,
            'score' => $row->score,
            'last_solved_at' => $row->last_solved_at
                ? Carbon::parse($row->last_solved_at)->format('d M H:i')
                : null,
        ])->all();
    }

    /**
     * The score of the best teams over time, for the scoreboard chart.
     * Every point is [milliseconds since epoch, score].
     *
     * @return list<array{name: string, points: list<array{int, int}>}>
     */
    public function series(Competition $competition, int $limit = 8): array
    {
        $teams = $this->ranking($competition)->take($limit);
        $start = $competition->start_at->getTimestampMs();
        $end = min(now(), $competition->end_at)->getTimestampMs();

        $solves = Submission::query()
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->where('challenges.competition_id', $competition->id)
            ->where('submissions.is_correct', true)
            ->whereIn('submissions.team_id', $teams->pluck('team.id'))
            ->select('submissions.team_id', 'submissions.submitted_at as at', 'challenges.points as delta')
            ->get();

        $hints = DB::table('hint_unlocks')
            ->join('hints', 'hints.id', '=', 'hint_unlocks.hint_id')
            ->join('challenges', 'challenges.id', '=', 'hints.challenge_id')
            ->where('challenges.competition_id', $competition->id)
            ->whereIn('hint_unlocks.team_id', $teams->pluck('team.id'))
            ->select('hint_unlocks.team_id', 'hint_unlocks.unlocked_at as at', DB::raw('-hints.cost as delta'))
            ->get();

        return $teams->map(function ($row) use ($solves, $hints, $start, $end) {
            $events = $solves->where('team_id', $row->team->id)
                ->concat($hints->where('team_id', $row->team->id))
                ->map(fn ($event) => [Carbon::parse($event->at)->getTimestampMs(), (int) $event->delta])
                ->sortBy(0)
                ->values();

            $score = 0;
            $points = [[$start, 0]];

            foreach ($events as [$at, $delta]) {
                $score += $delta;
                $points[] = [max($at, $start), $score];
            }

            $points[] = [max($end, $start), $score];

            return ['name' => $row->team->name, 'points' => $points];
        })->all();
    }

    /**
     * The first team that solved each challenge.
     *
     * @return Collection<int, object{challenge_id: int, team_id: int, team: string, at: string}>
     */
    public function firstBloods(Competition $competition): Collection
    {
        $rows = Submission::query()
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->join('teams', 'teams.id', '=', 'submissions.team_id')
            ->where('challenges.competition_id', $competition->id)
            ->where('submissions.is_correct', true)
            ->orderBy('submissions.submitted_at')
            ->select('submissions.challenge_id', 'submissions.team_id', 'teams.name as team', 'submissions.submitted_at as at')
            ->get();

        return $rows->unique('challenge_id')->keyBy('challenge_id');
    }

    /**
     * How many teams solved each challenge.
     *
     * @return Collection<int, int>
     */
    public function solveCounts(Competition $competition): Collection
    {
        return Submission::query()
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->where('challenges.competition_id', $competition->id)
            ->where('submissions.is_correct', true)
            ->groupBy('submissions.challenge_id')
            ->selectRaw('submissions.challenge_id as challenge_id, COUNT(*) as total')
            ->pluck('total', 'challenge_id');
    }

    /**
     * @return array{challenges: int, teams: int, solves: int, attempts: int, points: int}
     */
    public function stats(Competition $competition): array
    {
        $published = $competition->challenges()->where('is_published', true);

        $submissions = Submission::query()
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->where('challenges.competition_id', $competition->id);

        return [
            'challenges' => (clone $published)->count(),
            'teams' => $competition->teams()->count(),
            'solves' => (clone $submissions)->where('submissions.is_correct', true)->count(),
            'attempts' => (clone $submissions)->count(),
            'points' => (int) (clone $published)->sum('points'),
        ];
    }

    /**
     * @return array{competitions: int, challenges: int, teams: int, players: int, solves: int}
     */
    public function globalStats(): array
    {
        return [
            'competitions' => Competition::where('status', '!=', 'draft')->count(),
            'challenges' => Challenge::where('is_published', true)->count(),
            'teams' => Team::count(),
            'players' => User::where('role', 'player')->count(),
            'solves' => Submission::where('is_correct', true)->count(),
        ];
    }

    /**
     * Recent events: solves (with first blood), unlocked hints, registrations, and writeups.
     * Limit the feed to one competition and/or one team when needed.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function activity(?Competition $competition = null, ?Team $team = null, ?int $limit = null): Collection
    {
        $limit ??= (int) config('flagboard.activity_limit');
        $events = collect();

        $solves = Submission::query()
            ->join('challenges', 'challenges.id', '=', 'submissions.challenge_id')
            ->join('teams', 'teams.id', '=', 'submissions.team_id')
            ->where('submissions.is_correct', true)
            ->when($competition, fn ($query) => $query->where('challenges.competition_id', $competition->id))
            ->when($team, fn ($query) => $query->where('submissions.team_id', $team->id))
            ->orderByDesc('submissions.submitted_at')
            ->limit($limit)
            ->select('submissions.challenge_id', 'submissions.team_id', 'submissions.submitted_at as at', 'teams.name as team', 'challenges.title', 'challenges.points')
            ->get();

        $earliest = Submission::where('is_correct', true)
            ->whereIn('challenge_id', $solves->pluck('challenge_id'))
            ->groupBy('challenge_id')
            ->selectRaw('challenge_id, MIN(submitted_at) as first_at')
            ->pluck('first_at', 'challenge_id');

        foreach ($solves as $solve) {
            $events->push([
                'type' => 'solve',
                'at' => Carbon::parse($solve->at),
                'team' => $solve->team,
                'team_id' => $solve->team_id,
                'title' => $solve->title,
                'points' => (int) $solve->points,
                'challenge_id' => $solve->challenge_id,
                'first_blood' => ($earliest[$solve->challenge_id] ?? null) === $solve->at,
            ]);
        }

        $unlocks = DB::table('hint_unlocks')
            ->join('hints', 'hints.id', '=', 'hint_unlocks.hint_id')
            ->join('challenges', 'challenges.id', '=', 'hints.challenge_id')
            ->join('teams', 'teams.id', '=', 'hint_unlocks.team_id')
            ->when($competition, fn ($query) => $query->where('challenges.competition_id', $competition->id))
            ->when($team, fn ($query) => $query->where('hint_unlocks.team_id', $team->id))
            ->orderByDesc('hint_unlocks.unlocked_at')
            ->limit($limit)
            ->select('hint_unlocks.team_id', 'hint_unlocks.unlocked_at as at', 'teams.name as team', 'challenges.title', 'hints.cost', 'challenges.id as challenge_id')
            ->get();

        foreach ($unlocks as $unlock) {
            $events->push([
                'type' => 'hint',
                'at' => Carbon::parse($unlock->at),
                'team' => $unlock->team,
                'team_id' => $unlock->team_id,
                'title' => $unlock->title,
                'cost' => (int) $unlock->cost,
                'challenge_id' => $unlock->challenge_id,
            ]);
        }

        $registrations = DB::table('competition_team')
            ->join('competitions', 'competitions.id', '=', 'competition_team.competition_id')
            ->join('teams', 'teams.id', '=', 'competition_team.team_id')
            ->where('competitions.status', '!=', 'draft')
            ->when($competition, fn ($query) => $query->where('competitions.id', $competition->id))
            ->when($team, fn ($query) => $query->where('teams.id', $team->id))
            ->orderByDesc('competition_team.registered_at')
            ->limit(max(2, intdiv($limit, 4)))
            ->select('teams.id as team_id', 'competition_team.registered_at as at', 'teams.name as team', 'competitions.title', 'competitions.id as competition_id')
            ->get();

        foreach ($registrations as $registration) {
            $events->push([
                'type' => 'registration',
                'at' => Carbon::parse($registration->at),
                'team' => $registration->team,
                'team_id' => $registration->team_id,
                'title' => $registration->title,
                'competition_id' => $registration->competition_id,
            ]);
        }

        if (! $team) {
            $writeups = Writeup::query()
                ->where('writeups.status', 'published')
                ->join('users', 'users.id', '=', 'writeups.user_id')
                ->join('challenges', 'challenges.id', '=', 'writeups.challenge_id')
                ->when($competition, fn ($query) => $query->where('challenges.competition_id', $competition->id))
                ->orderByDesc('writeups.created_at')
                ->limit(max(2, intdiv($limit, 4)))
                ->select('writeups.id', 'writeups.created_at as at', 'users.name as author', 'challenges.title', 'writeups.title as writeup')
                ->get();

            foreach ($writeups as $writeup) {
                $events->push([
                    'type' => 'writeup',
                    'at' => Carbon::parse($writeup->at),
                    'author' => $writeup->author,
                    'title' => $writeup->title,
                    'writeup' => $writeup->writeup,
                    'writeup_id' => $writeup->id,
                ]);
            }
        }

        return $events->sortByDesc(fn ($event) => $event['at']->getTimestamp())->take($limit)->values();
    }
}
