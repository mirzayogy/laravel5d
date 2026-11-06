@extends('layouts.app')

@section('title', __('Reset password'))

@section('content')
    <div class="rise mx-auto max-w-sm">
        <div class="mb-6 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-brand-600 text-xl text-white">🔑</div>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ __('Choose a new password') }}</h1>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="card space-y-4 p-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email" class="label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required class="input">
            </div>
            <div>
                <label for="password" class="label">{{ __('New password') }}</label>
                <input id="password" name="password" type="password" required minlength="8" class="input">
            </div>
            <div>
                <label for="password_confirmation" class="label">{{ __('Confirm password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="input">
            </div>
            <button type="submit" class="btn btn-primary w-full">{{ __('Reset password') }}</button>
        </form>
    </div>
@endsection
