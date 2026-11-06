@extends('layouts.app')

@section('title', __('Log in'))

@section('content')
    <div class="rise mx-auto max-w-sm">
        <div class="mb-6 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-brand-600 text-xl text-white">☕</div>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ __('Welcome back') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('Masuk untuk memesan dan melihat riwayat pesananmu.') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card space-y-4 p-6">
            @csrf
            <div>
                <label for="email" class="label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input">
            </div>
            <div>
                <label for="password" class="label">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" required class="input">
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-stone-600">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-stone-300 accent-brand-600"> {{ __('Remember me') }}
                </label>
                <a href="{{ route('password.request') }}" class="text-brand-700 hover:underline">{{ __('Forgot password?') }}</a>
            </div>
            <button type="submit" class="btn btn-primary w-full">{{ __('Log in') }}</button>
        </form>

        <p class="mt-4 text-center text-sm text-stone-500">{{ __('No account yet?') }} <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:underline">{{ __('Register') }}</a></p>

        <div class="mt-6 rounded-xl border border-dashed border-stone-300 bg-white p-4 text-xs text-stone-500">
            <div class="mb-1 font-medium text-stone-600">{{ __('Demo accounts (password: password)') }}</div>
            {{ __('customer@example.com · cashier@example.com · admin@example.com') }}
        </div>
    </div>
@endsection
