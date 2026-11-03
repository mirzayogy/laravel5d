<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Notifications\AppNotification;
use App\Services\ContestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct(private ContestService $contest) {}

    /** The public directory of all teams. */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $teams = Team::query()
            ->with('captain')
            ->withCount([
                'users',
                'submissions as solves_count' => fn ($query) => $query->where('is_correct', true),
                'competitions',
            ])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderByDesc('solves_count')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('teams.index', ['teams' => $teams, 'search' => $search]);
    }

    /** The public page of one team. */
    public function profile(Team $team): View
    {
        $team->load(['users.profile', 'captain']);

        $standings = $team->competitions()
            ->where('status', '!=', 'draft')
            ->orderByDesc('start_at')
            ->get()
            ->map(fn ($competition) => [
                'competition' => $competition,
                'standing' => $this->contest->standing($team, $competition),
            ]);

        $solves = Submission::where('team_id', $team->id)
            ->where('is_correct', true)
            ->with('challenge.category', 'challenge.competition', 'user')
            ->orderByDesc('submitted_at')
            ->limit(12)
            ->get();

        return view('teams.profile', [
            'team' => $team,
            'standings' => $standings,
            'solves' => $solves,
            'solveCount' => Submission::where('team_id', $team->id)->where('is_correct', true)->count(),
        ]);
    }

    /** The team of the signed-in user, or the forms to create or join one. */
    public function show(Request $request): View
    {
        $user = $request->user();
        $team = $user->team?->load(['users.profile', 'captain', 'competitions']);

        $solves = $team ? Submission::where('team_id', $team->id)->where('is_correct', true)->count() : 0;

        $standings = $team
            ? $team->competitions->where('status', '!=', 'draft')->sortByDesc('start_at')->map(fn ($competition) => [
                'competition' => $competition,
                'standing' => $this->contest->standing($team, $competition),
            ])
            : collect();

        $topTeams = Team::query()
            ->withCount(['users', 'submissions as solves_count' => fn ($query) => $query->where('is_correct', true)])
            ->orderByDesc('solves_count')
            ->limit(5)
            ->get();

        return view('teams.show', [
            'team' => $team,
            'isCaptain' => $team?->isCaptain($user) ?? false,
            'solves' => $solves,
            'standings' => $standings,
            'topTeams' => $topTeams,
            'size' => config('flagboard.team_size'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->team_id) {
            return back()->with('error', 'You already belong to a team.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:teams,name'],
        ]);

        $team = Team::create([
            'name' => $data['name'],
            'invite_code' => $this->newInviteCode(),
            'captain_id' => $user->id,
        ]);

        $user->update(['team_id' => $team->id]);

        return redirect()->route('team.show')->with('success', 'Team created. Share the invite code with your friends.');
    }

    public function join(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->team_id) {
            return back()->with('error', 'You already belong to a team.');
        }

        $data = $request->validate([
            'invite_code' => ['required', 'string', 'max:20'],
        ]);

        $team = Team::where('invite_code', Str::upper(trim($data['invite_code'])))->first();

        if (! $team) {
            return back()->withErrors(['invite_code' => 'The invite code is not valid.'])->withInput();
        }

        if ($team->isFull()) {
            return back()->withErrors(['invite_code' => 'This team is full (maximum '.config('flagboard.team_size').' members).'])->withInput();
        }

        $user->update(['team_id' => $team->id]);

        $team->users()->whereKeyNot($user->id)->get()->each(fn (User $mate) => $mate->notify(new AppNotification(
            'New teammate',
            $user->name.' joined '.$team->name.'.',
            route('team.show'),
            'user-plus',
            'green',
        )));

        return redirect()->route('team.show')->with('success', 'You joined '.$team->name.'.');
    }

    public function update(Request $request): RedirectResponse
    {
        $team = $this->captainTeam($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('teams', 'name')->ignore($team->id)],
        ]);

        $team->update($data);

        return back()->with('success', 'Team renamed.');
    }

    public function regenerateCode(Request $request): RedirectResponse
    {
        $this->captainTeam($request)->update(['invite_code' => $this->newInviteCode()]);

        return back()->with('success', 'A new invite code is ready. The old one no longer works.');
    }

    public function removeMember(Request $request, User $user): RedirectResponse
    {
        $team = $this->captainTeam($request);

        abort_unless($user->team_id === $team->id, 404);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Use "Leave team" to leave your own team.');
        }

        $user->update(['team_id' => null]);

        $user->notify(new AppNotification(
            'Removed from a team',
            'The captain of '.$team->name.' removed you from the team.',
            route('team.show'),
            'user-minus',
            'coral',
        ));

        return back()->with('success', $user->name.' was removed from the team.');
    }

    public function transferCaptain(Request $request, User $user): RedirectResponse
    {
        $team = $this->captainTeam($request);

        abort_unless($user->team_id === $team->id, 404);

        $team->update(['captain_id' => $user->id]);

        $user->notify(new AppNotification(
            'You are the captain now',
            $request->user()->name.' made you the captain of '.$team->name.'.',
            route('team.show'),
            'crown',
            'amber',
        ));

        return back()->with('success', $user->name.' is the new captain.');
    }

    public function leave(Request $request): RedirectResponse
    {
        $user = $request->user();
        $team = $user->team;

        if (! $team) {
            return redirect()->route('team.show');
        }

        $others = $team->users()->whereKeyNot($user->id)->orderBy('id')->get();

        $user->update(['team_id' => null]);

        if ($others->isEmpty()) {
            // An empty team with history stays, so the scoreboards keep their numbers.
            if ($team->submissions()->doesntExist() && $team->competitions()->doesntExist()) {
                $team->delete();
            } else {
                $team->update(['captain_id' => null]);
            }
        } elseif ($team->isCaptain($user)) {
            $heir = $others->first();
            $team->update(['captain_id' => $heir->id]);

            $heir->notify(new AppNotification(
                'You are the captain now',
                $user->name.' left '.$team->name.', so you lead the team.',
                route('team.show'),
                'crown',
                'amber',
            ));
        }

        return redirect()->route('team.show')->with('success', 'You left the team.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $team = $this->captainTeam($request);

        if ($team->submissions()->exists()) {
            return back()->with('error', 'A team with submissions cannot be disbanded, because the scoreboards need its history.');
        }

        $team->users()->whereKeyNot($request->user()->id)->get()->each(fn (User $mate) => $mate->notify(new AppNotification(
            'Team disbanded',
            $team->name.' was disbanded by its captain.',
            route('team.show'),
            'users',
            'coral',
        )));

        $team->users()->update(['team_id' => null]);
        $team->delete();

        return redirect()->route('team.show')->with('success', 'The team was disbanded.');
    }

    private function captainTeam(Request $request): Team
    {
        $team = $request->user()->team;

        abort_unless($team && $team->isCaptain($request->user()), 403, 'Only the captain can do this.');

        return $team;
    }

    private function newInviteCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (Team::where('invite_code', $code)->exists());

        return $code;
    }
}
