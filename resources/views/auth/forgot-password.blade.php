@extends('layouts.app')

@section('title', __('Forgot password'))

@section('content')
    <div class="rise mx-auto max-w-sm">
        <div class="mb-6 text-center">
            <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-brand-600 text-xl text-white">🔑</div>
            <h1 class="mt-4 text-2xl font-semibold tracking-tight">{{ __('Forgot your password?') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('Masukkan emailmu dan kami kirim tautan untuk membuat password baru.') }}</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="card space-y-4 p-6">
            @csrf
            <div>
                <label for="email" class="label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input">
            </div>
            <button type="submit" class="btn btn-primary w-full">{{ __('Send reset link') }}</button>
        </form>

        <p class="mt-4 text-center text-sm text-stone-500"><a href="{{ route('login') }}" class="font-medium text-brand-700 hover:underline">{{ __('Back to log in') }}</a></p>
    </div>
@endsection
