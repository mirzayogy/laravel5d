@extends('layouts.app')

@section('title', 'Teams')

@section('content')
    <x-page-header title="Teams" subtitle="Every team on Flagboard, ranked by solved challenges." icon="users">
        <x-slot:actions>
            @auth
                <x-button :href="route('team.show')" variant="accent" icon="user-plus">{{ auth()->user()->team_id ? 'My team' : 'Create or join' }}</x-button>
            @endauth
        </x-slot:actions>
    </x-page-header>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('teams.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-56 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search teams" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <x-button type="submit" icon="search">Search</x-button>
        </form>
    </x-card>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($teams as $team)
            <x-card :hover="true" class="relative">
                <div class="flex items-center gap-3">
                    <x-avatar :name="$team->name" size="lg" />
                    <div class="min-w-0">
                        <a href="{{ route('teams.show', $team) }}" class="block truncate text-lg font-extrabold tracking-tight after:absolute after:inset-0 after:rounded-3xl hover:text-sky-700">{{ $team->name }}</a>
                        <div class="truncate text-xs text-slate-500">Captain: {{ $team->captain?->name ?? 'none' }}</div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-2xl bg-sky-50 py-2"><div class="text-lg font-extrabold tabular-nums">{{ $team->users_count }}</div><div class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Members</div></div>
                    <div class="rounded-2xl bg-emerald-50 py-2"><div class="text-lg font-extrabold tabular-nums">{{ $team->solves_count }}</div><div class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Solves</div></div>
                    <div class="rounded-2xl bg-coral-50 py-2"><div class="text-lg font-extrabold tabular-nums">{{ $team->competitions_count }}</div><div class="text-[10px] font-bold tracking-wide text-slate-500 uppercase">Events</div></div>
                </div>
            </x-card>
        @empty
            <div class="sm:col-span-2 lg:col-span-3"><x-empty icon="users" title="No team found" text="Try a different name." /></div>
        @endforelse
    </div>

    <div class="mt-6">{{ $teams->links('pagination.custom') }}</div>
@endsection
