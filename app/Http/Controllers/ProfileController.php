<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()->load('profile')]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'bio' => ['nullable', 'string', 'max:500'],
            'timezone' => ['required', Rule::in(array_keys(config('habitude.timezones')))],
            'daily_goal' => ['required', 'integer', 'min:1', 'max:20'],
            'locale' => ['nullable', Rule::in(array_keys(config('habitude.locales')))],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => __('This email is already registered.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ]);

        $user->fill(collect($data)->only(['name', 'email'])->all());
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        $user->profile()->updateOrCreate([], collect($data)->only(['bio', 'timezone', 'daily_goal', 'locale'])->filter()->all() + ['email_reminders' => $request->boolean('email_reminders')]);

        if (! empty($data['locale'])) {
            $request->session()->put('locale', $data['locale']);
            App::setLocale($data['locale']);
        }

        return redirect()->route('profile.edit')->with('success', __('Profile updated.'));
    }
}
