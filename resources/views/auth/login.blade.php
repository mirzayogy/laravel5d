@extends('layouts.app')

@section('title', 'Log in')

@section('content')
    <div class="mx-auto max-w-sm">
        <h1 class="text-xl font-semibold">Log in</h1>

        <form method="POST" action="{{ route('login') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <button type="submit" class="w-full rounded bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-500">Log in</button>
        </form>

        <p class="mt-4 text-sm text-slate-500">No account yet? <a href="{{ route('register') }}" class="text-indigo-700 underline">Register</a></p>
    </div>
@endsection
