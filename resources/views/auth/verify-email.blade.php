@extends('layouts.app')

@section('title', 'Verify your email')

@section('content')
    <div class="mx-auto max-w-xl">
        <x-card class="p-8 text-center">
            <span class="mx-auto flex size-16 animate-float items-center justify-center rounded-3xl bg-sky-500 text-white shadow-[0_5px_0_0_var(--color-sky-700)]"><x-icon name="mail" class="size-8" /></span>
            <h1 class="mt-5 text-2xl font-extrabold tracking-tight">Check your inbox</h1>
            <p class="mt-2 text-sm text-slate-600">
                We sent a verification link to <span class="font-bold">{{ auth()->user()->email }}</span>.
                Open it to register teams, submit flags, and write writeups.
            </p>

            @if (session('status'))
                <div class="mt-4 rounded-2xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
            @endif

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-button type="submit" icon="send">Resend the email</x-button>
                </form>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="ghost" icon="log-out">Log out</x-button>
                </form>
            </div>

            <p class="mt-6 rounded-2xl bg-sky-50 px-4 py-3 text-xs text-slate-500">
                Developing locally? With <code class="rounded bg-white px-1 font-mono">MAIL_MAILER=log</code> the email is written to <code class="rounded bg-white px-1 font-mono">storage/logs/laravel.log</code>.
            </p>
        </x-card>
    </div>
@endsection
