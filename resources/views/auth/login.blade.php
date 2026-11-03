@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="grid items-stretch gap-6 lg:grid-cols-2">
        @include('partials.auth-aside')

        <x-card class="mx-auto w-full max-w-md self-center p-7 lg:max-w-none">
            <h1 class="text-2xl font-extrabold tracking-tight">Welcome back</h1>
            <p class="mt-1 text-sm text-slate-500">Log in to continue where you left off.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <x-input name="email" type="email" label="Email" required autofocus autocomplete="email" />
                <x-input name="password" type="password" label="Password" required autocomplete="current-password" />
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 font-medium text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-sky-300 text-sky-500"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="font-semibold text-sky-700 hover:underline">Forgot password?</a>
                </div>
                <x-button type="submit" class="w-full" size="lg" icon="key">Log in</x-button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500">New here? <a href="{{ route('register') }}" class="font-bold text-sky-700 hover:underline">Create an account</a></p>

            <div class="mt-6 rounded-2xl bg-sky-50 px-4 py-3 text-xs text-slate-600">
                <div class="mb-1 font-bold text-slate-700">Demo accounts (password: password)</div>
                <div class="grid grid-cols-2 gap-x-3 gap-y-0.5">
                    <span>player@example.com</span><span class="text-slate-400">team captain</span>
                    <span>member@example.com</span><span class="text-slate-400">team member</span>
                    <span>solo@example.com</span><span class="text-slate-400">no team yet</span>
                    <span>admin@example.com</span><span class="text-slate-400">admin</span>
                </div>
            </div>
        </x-card>
    </div>
@endsection
