@extends('layouts.app')

@section('title', $user ? __('Edit user') : __('New user'))

@section('content')
    <x-page-header :title="$user ? __('Edit user') : __('New user')" :subtitle="$user ? $user->email : __('Create a new account and set its role.')" />

    <form method="POST" action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}" class="rise card max-w-2xl space-y-5 p-6">
        @csrf
        @if ($user)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="label">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user?->name) }}" required class="input">
            </div>
            <div>
                <label for="email" class="label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" required class="input">
            </div>
            <div>
                <label for="role" class="label">{{ __('Role') }}</label>
                <select id="role" name="role" required class="input">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $user?->role ?? 'customer') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="password" class="label">{{ __('Password') }} {{ $user ? '(leave blank to keep)' : '' }}</label>
                <input id="password" name="password" type="password" minlength="8" @required(! $user) autocomplete="new-password" class="input">
            </div>
            <div>
                <label for="phone" class="label">{{ __('Phone') }}</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user?->profile?->phone) }}" class="input">
            </div>
            <div>
                <label for="address" class="label">{{ __('Address') }}</label>
                <input id="address" name="address" type="text" value="{{ old('address', $user?->profile?->address) }}" class="input">
            </div>
        </div>

        <div class="flex gap-2 border-t border-stone-100 pt-5">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">{{ __('Back') }}</a>
        </div>
    </form>
@endsection
