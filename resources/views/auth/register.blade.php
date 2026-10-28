@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="mx-auto max-w-sm">
        <h1 class="text-xl font-semibold">Create an account</h1>

        <form method="POST" action="{{ route('register') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <button type="submit" class="w-full rounded bg-indigo-600 px-3 py-2 text-white hover:bg-indigo-500">Register</button>
        </form>

        <p class="mt-4 text-sm text-slate-500">Already registered? <a href="{{ route('login') }}" class="text-indigo-700 underline">Log in</a></p>
    </div>
@endsection
