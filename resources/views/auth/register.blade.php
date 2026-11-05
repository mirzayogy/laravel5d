@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="rise mx-auto max-w-sm">
        <div class="mb-6 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-brand-600 text-xl text-white">☕</div>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">Create an account</h1>
            <p class="mt-1 text-sm text-stone-500">Daftar gratis dan mulai pesan menu favoritmu.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="card space-y-4 p-6">
            @csrf
            <div>
                <label for="name" class="label">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="input">
            </div>
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input">
            </div>
            <div>
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" required minlength="8" class="input">
            </div>
            <div>
                <label for="password_confirmation" class="label">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="input">
            </div>
            <button type="submit" class="btn btn-primary w-full">Register</button>
        </form>

        <p class="mt-4 text-center text-sm text-stone-500">Already registered? <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">Log in</a></p>
    </div>
@endsection
