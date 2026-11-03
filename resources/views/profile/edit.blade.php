@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <x-page-header title="My profile" subtitle="How you appear to your team and on the writeups you publish." icon="user" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="mb-4 text-lg font-extrabold tracking-tight">Personal details</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input name="name" label="Name" :value="$user->name" required />
                        <x-input name="email" type="email" label="Email" :value="$user->email" disabled hint="The email cannot be changed here." />
                    </div>
                    <x-textarea name="bio" label="Short bio" :value="$user->profile?->bio" rows="2" maxlength="255" placeholder="What do you like to hack?" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input name="institution" label="School or company" :value="$user->profile?->institution" />
                        <x-input name="github_url" type="url" label="GitHub profile" :value="$user->profile?->github_url" placeholder="https://github.com/you" />
                    </div>
                    <x-button type="submit" icon="check">Save profile</x-button>
                </form>
            </x-card>

            <x-card>
                <h2 class="mb-4 text-lg font-extrabold tracking-tight">Change password</h2>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <x-input name="current_password" type="password" label="Current password" required autocomplete="current-password" />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input name="password" type="password" label="New password" required autocomplete="new-password" hint="At least 8 characters." />
                        <x-input name="password_confirmation" type="password" label="Confirm new password" required autocomplete="new-password" />
                    </div>
                    <x-button type="submit" variant="ghost" icon="lock">Change password</x-button>
                </form>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="text-center">
                <x-avatar :name="$user->name" size="lg" class="mx-auto" />
                <div class="mt-3 text-lg font-extrabold">{{ $user->name }}</div>
                <div class="text-sm text-slate-500">{{ $user->email }}</div>
                <div class="mt-2 flex flex-wrap justify-center gap-1.5">
                    <x-badge :tone="$user->isAdmin() ? 'amber' : 'sky'">{{ $user->role }}</x-badge>
                    @if ($user->hasVerifiedEmail())<x-badge tone="green"><x-icon name="check" class="size-3" /> Verified</x-badge>@else<x-badge tone="coral">Not verified</x-badge>@endif
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-2xl bg-emerald-50 py-2"><div class="text-lg font-extrabold tabular-nums">{{ $solves }}</div><div class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Solves</div></div>
                    <div class="rounded-2xl bg-sky-50 py-2"><div class="text-lg font-extrabold tabular-nums">{{ $attempts }}</div><div class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Tries</div></div>
                    <div class="rounded-2xl bg-violet-50 py-2"><div class="text-lg font-extrabold tabular-nums">{{ $writeups }}</div><div class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Writeups</div></div>
                </div>
            </x-card>

            <x-card tone="sky">
                <h2 class="font-extrabold">Team</h2>
                @if ($user->team)
                    <div class="mt-2 flex items-center gap-3">
                        <x-avatar :name="$user->team->name" />
                        <a href="{{ route('team.show') }}" class="font-bold hover:text-sky-700">{{ $user->team->name }}</a>
                    </div>
                @else
                    <p class="mt-1 text-sm text-slate-600">You are not in a team yet.</p>
                    <x-button :href="route('team.show')" size="sm" class="mt-3" icon="user-plus">Find a team</x-button>
                @endif
            </x-card>

            @unless ($user->hasVerifiedEmail())
                <x-card tone="coral">
                    <h2 class="font-extrabold">Verify your email</h2>
                    <p class="mt-1 text-sm text-slate-600">Verified players can register teams, submit flags, and write writeups.</p>
                    <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
                        @csrf
                        <x-button type="submit" size="sm" variant="accent" icon="send">Send the link</x-button>
                    </form>
                </x-card>
            @endunless
        </div>
    </div>
@endsection
