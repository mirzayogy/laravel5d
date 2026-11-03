<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load('profile', 'team');

        $solves = $user->team
            ? DB::table('submissions')->where('user_id', $user->id)->where('is_correct', true)->count()
            : 0;

        return view('profile.edit', [
            'user' => $user,
            'solves' => $solves,
            'attempts' => $user->submissions()->count(),
            'writeups' => $user->writeups()->count(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
        ]);

        $user = $request->user();

        $user->update(['name' => $data['name']]);
        $user->profile()->updateOrCreate([], [
            'bio' => $data['bio'] ?? null,
            'institution' => $data['institution'] ?? null,
            'github_url' => $data['github_url'] ?? null,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return redirect()->route('profile.edit')->with('success', 'Password changed.');
    }
}
