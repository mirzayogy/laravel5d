<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function show(Request $request): View
    {
        $team = $request->user()->team;

        $team?->load(['users', 'competitions']);

        return view('teams.show', compact('team'));
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

        do {
            $code = Str::upper(Str::random(8));
        } while (Team::where('invite_code', $code)->exists());

        $team = Team::create([
            'name' => $data['name'],
            'invite_code' => $code,
        ]);

        $user->update(['team_id' => $team->id]);

        return redirect()->route('team.show')->with('success', 'Team created.');
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

        $user->update(['team_id' => $team->id]);

        return redirect()->route('team.show')->with('success', 'You joined '.$team->name.'.');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->user()->update(['team_id' => null]);

        return redirect()->route('team.show')->with('success', 'You left the team.');
    }
}
