<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $role = in_array($request->query('role'), [User::ROLE_USER, User::ROLE_ADMIN], true) ? $request->query('role') : '';

        $users = User::query()
            ->withCount(['habits', 'moodEntries', 'journalEntries'])
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->when($role !== '', fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user->loadCount(['habits', 'moodEntries', 'journalEntries', 'achievements']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
            'password' => ['nullable', Password::min(8)],
        ], [
            'email.unique' => __('This email is already registered.'),
        ]);

        // Nobody can lock themselves out of the console.
        if ($user->is($request->user()) && $data['role'] !== $user->role) {
            return back()->withInput()->withErrors(['role' => __('You cannot change your own role.')]);
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        $user->role = $data['role'];
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('success', __('User updated.'));
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return redirect()->route('admin.users.index')->with('error', __('You cannot delete your own account here.'));
        }

        // Habits, logs, moods, journals and reminders are removed by the cascade rules.
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted together with all of their data.'));
    }
}
