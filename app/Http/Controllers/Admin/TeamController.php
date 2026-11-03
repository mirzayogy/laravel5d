<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $teams = Team::with('captain')
            ->withCount([
                'users',
                'competitions',
                'submissions as solves_count' => fn ($query) => $query->where('is_correct', true),
            ])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderByDesc('solves_count')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.teams.index', compact('teams', 'search'));
    }

    public function destroy(Team $team): RedirectResponse
    {
        if ($team->submissions()->exists()) {
            return back()->with('error', 'This team has submissions, which the scoreboards need. It cannot be deleted.');
        }

        $team->users()->update(['team_id' => null]);
        $team->delete();

        return back()->with('success', 'Team deleted. Its members are free to join another team.');
    }
}
