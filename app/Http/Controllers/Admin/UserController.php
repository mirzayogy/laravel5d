<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $role = $request->query('role');
        $verified = $request->query('verified');

        $users = User::with('team')
            ->withCount(['submissions as solves_count' => fn ($query) => $query->where('is_correct', true)])
            ->when($search !== '', fn ($query) => $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
            }))
            ->when(in_array($role, ['admin', 'player'], true), fn ($query) => $query->where('role', $role))
            ->when($verified === 'yes', fn ($query) => $query->whereNotNull('email_verified_at'))
            ->when($verified === 'no', fn ($query) => $query->whereNull('email_verified_at'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'verified' => $verified,
            'admins' => User::where('role', 'admin')->count(),
            'total' => User::count(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'player'])],
        ]);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        if ($user->isAdmin() && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'The last admin cannot be demoted.');
        }

        $user->update(['role' => $data['role']]);

        $user->notify(new AppNotification(
            'Your role changed',
            'You are now '.($data['role'] === 'admin' ? 'an admin' : 'a player').'.',
            route('dashboard'),
            'shield',
            'amber',
        ));

        return back()->with('success', $user->name.' is now '.($data['role'] === 'admin' ? 'an admin' : 'a player').'.');
    }

    public function verify(User $user): RedirectResponse
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return back()->with('success', 'The email of '.$user->name.' is marked as verified.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'The last admin cannot be deleted.');
        }

        if ($user->submissions()->exists()) {
            return back()->with('error', 'This user has submissions, which the scoreboards need. Keep the account instead.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
