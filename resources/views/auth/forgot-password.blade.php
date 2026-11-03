@extends('layouts.app')

@section('title', 'Forgot password')

@section('content')
    <div class="grid items-stretch gap-6 lg:grid-cols-2">
        @include('partials.auth-aside')

        <x-card class="mx-auto w-full max-w-md self-center p-7 lg:max-w-none">
            <span class="flex size-12 items-center justify-center rounded-2xl bg-sky-100 text-sky-700"><x-icon name="mail" class="size-6" /></span>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight">Forgot your password?</h1>
            <p class="mt-1 text-sm text-slate-500">Enter your email address and we will send you a link to choose a new one.</p>

            @if (session('status'))
                <div class="mt-4 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf
                <x-input name="email" type="email" label="Email" required autofocus />
                <x-button type="submit" class="w-full" size="lg" icon="send">Send reset link</x-button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500"><a href="{{ route('login') }}" class="font-bold text-sky-700 hover:underline">Back to log in</a></p>
        </x-card>
    </div>
@endsection
