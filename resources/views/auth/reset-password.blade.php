@extends('layouts.app')

@section('title', 'Choose a new password')

@section('content')
    <div class="grid items-stretch gap-6 lg:grid-cols-2">
        @include('partials.auth-aside')

        <x-card class="mx-auto w-full max-w-md self-center p-7 lg:max-w-none">
            <span class="flex size-12 items-center justify-center rounded-2xl bg-coral-100 text-coral-700"><x-icon name="lock" class="size-6" /></span>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight">Choose a new password</h1>
            <p class="mt-1 text-sm text-slate-500">Pick something you have not used before.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-input name="email" type="email" label="Email" :value="$email" required />
                <x-input name="password" type="password" label="New password" hint="At least 8 characters." required autofocus />
                <x-input name="password_confirmation" type="password" label="Confirm new password" required />
                <x-button type="submit" variant="accent" class="w-full" size="lg" icon="check">Change password</x-button>
            </form>
        </x-card>
    </div>
@endsection
