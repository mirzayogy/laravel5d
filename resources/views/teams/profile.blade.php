@extends('layouts.app')

@section('title', $team->name)

@section('content')
    <x-page-header :title="$team->name" :subtitle="'Captain: '.($team->captain?->name ?? 'none')" icon="users" :crumbs="['Teams' => route('teams.index'), $team->name => null]" />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat icon="check-circle" tone="green" label="Solves" :value="$solveCount" />
        <x-stat icon="users" tone="sky" label="Members" :value="$team->users->count()" :hint="'of '.config('flagboard.team_size')" />
        <x-stat icon="flag" tone="coral" label="Competitions" :value="$standings->count()" />
        <x-stat icon="trophy" tone="amber" label="Best rank" :value="$standings->map(fn ($row) => $row['standing']['rank'] ?? null)->filter()->min() ? '#'.$standings->map(fn ($row) => $row['standing']['rank'] ?? null)->filter()->min() : '-'" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="mb-3 text-lg font-extrabold tracking-tight">Standings</h2>
                <div class="space-y-3">
                    @forelse ($standings as $row)
                        <a href="{{ route('scoreboard.show', $row['competition']) }}" class="flex items-center gap-3 rounded-2xl border-2 border-sky-100 p-3 hover:border-sky-300">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-extrabold text-sky-800">{{ $row['standing'] && ! $row['competition']->isUpcoming() ? '#'.$row['standing']['rank'] : '-' }}</span>
                            <span class="min-w-0 grow">
                                <span class="block truncate font-bold">{{ $row['competition']->title }}</span>
                                <span class="text-xs text-slate-500">{{ $row['standing']['row']->solves ?? 0 }} solved</span>
                            </span>
                            <x-phase-badge :phase="$row['competition']->phase" />
                            <span class="w-16 text-right text-lg font-extrabold tabular-nums">{{ $row['standing']['row']->score ?? 0 }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">This team has not entered a competition yet.</p>
                    @endforelse
                </div>
            </x-card>

            <x-card>
                <h2 class="mb-1 text-lg font-extrabold tracking-tight">Recent solves</h2>
                <ul class="divide-y divide-sky-50">
                    @forelse ($solves as $solve)
                        <li class="flex items-center gap-3 py-3 text-sm">
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><x-icon name="check" class="size-4" /></span>
                            <div class="min-w-0 grow">
                                <div class="truncate font-semibold">{{ $solve->challenge->title }}</div>
                                <div class="text-xs text-slate-500">{{ $solve->challenge->competition->title }} by {{ $solve->user->name }}, {{ $solve->submitted_at->diffForHumans() }}</div>
                            </div>
                            <x-category-badge :name="$solve->challenge->category->name" class="hidden sm:inline-flex" />
                            <x-badge tone="green">+{{ $solve->challenge->points }}</x-badge>
                        </li>
                    @empty
                        <li class="py-4 text-sm text-slate-500">No solves yet.</li>
                    @endforelse
                </ul>
            </x-card>
        </div>

        <x-card class="self-start">
            <h2 class="mb-3 font-extrabold">Members</h2>
            <ul class="space-y-3">
                @foreach ($team->users as $member)
                    <li class="flex items-center gap-3">
                        <x-avatar :name="$member->name" />
                        <div class="min-w-0 grow">
                            <div class="flex items-center gap-1.5 text-sm font-bold">
                                <span class="truncate">{{ $member->name }}</span>
                                @if ($team->isCaptain($member))<x-icon name="crown" class="size-4 text-amber-500" />@endif
                            </div>
                            <div class="truncate text-xs text-slate-500">{{ $member->profile?->institution ?? 'No institution' }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
@endsection
