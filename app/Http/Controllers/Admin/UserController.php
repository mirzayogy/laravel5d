<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public const ROLES = ['customer', 'cashier', 'admin'];

    public function index(Request $request): View
    {
        $role = $request->query('role');
        $search = trim((string) $request->query('q', ''));

        $users = User::withCount('orders')
            ->when(in_array($role, self::ROLES, true), fn ($query) => $query->where('role', $role))
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $roles = self::ROLES;

        return view('admin.users.index', compact('users', 'roles', 'role', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => null, 'roles' => self::ROLES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        // accounts made by an admin do not need email verification
        $user->forceFill(['email_verified_at' => now()])->save();

        $user->profile()->create([
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('success', __('User created.'));
    }

    public function edit(User $user): View
    {
        $user->load('profile');

        return view('admin.users.form', ['user' => $user, 'roles' => self::ROLES]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($user->id === $request->user()->id && $data['role'] !== $user->role) {
            return back()->withInput()->withErrors(['role' => __('You cannot change your own role.')]);
        }

        $attributes = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $attributes['password'] = $data['password'];
        }

        $user->update($attributes);

        $user->profile()->updateOrCreate([], [
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('success', __('User updated.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        if ($user->orders()->exists()) {
            return back()->with('error', __('This user has orders and cannot be deleted.'));
        }

        $user->profile()->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('User deleted.'));
    }
}
