@extends('layouts.app')

@section('title', 'Create an account')

@section('content')
    <div class="grid items-stretch gap-6 lg:grid-cols-2">
        @include('partials.auth-aside')

        <x-card class="mx-auto w-full max-w-md self-center p-7 lg:max-w-none">
            <h1 class="text-2xl font-extrabold tracking-tight">Create your account</h1>
            <p class="mt-1 text-sm text-slate-500">It takes less than a minute. We will email you a link to verify your address.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                @csrf
                <x-input name="name" label="Name" required autofocus autocomplete="name" />
                <x-input name="email" type="email" label="Email" required autocomplete="email" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input name="password" type="password" label="Password" hint="At least 8 characters." required autocomplete="new-password" />
                    <x-input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />
                </div>
                <x-button type="submit" variant="accent" class="w-full" size="lg" icon="user-plus">Create account</x-button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500">Already registered? <a href="{{ route('login') }}" class="font-bold text-sky-700 hover:underline">Log in</a></p>
        </x-card>
    </div>
@endsection
