@extends('layouts.app')

@section('title', 'Admin')

@section('content')
    <x-page-header title="Admin" subtitle="Everything that happens on Flagboard, at a glance." icon="shield">
        <x-slot:actions>
            <x-button :href="route('admin.competitions.create')" variant="accent" icon="plus">New competition</x-button>
            <x-button :href="route('admin.challenges.create')" variant="ghost" icon="plus">New challenge</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.admin-nav')

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-stat icon="user" tone="sky" label="Users" :value="$stats['users']" />
        <x-stat icon="users" tone="violet" label="Teams" :value="$stats['teams']" />
        <x-stat icon="flag" tone="coral" label="Competitions" :value="$stats['competitions']" :hint="$stats['live'].' live now'" />
        <x-stat icon="target" tone="amber" label="Challenges" :value="$stats['challenges']" :hint="$stats['published'].' published'" />
        <x-stat icon="check-circle" tone="green" label="Solves" :value="$stats['solves']" :hint="$stats['attempts'].' attempts in total'" />
        <x-stat icon="file-text" tone="sky" label="Writeups" :value="$stats['writeups']" hint="published" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <div class="mb-3 flex items-center gap-2">
                    <x-icon name="bar-chart" class="size-5 text-sky-600" />
                    <h2 class="text-lg font-extrabold tracking-tight">Submissions in the last 14 days</h2>
                </div>
                <div class="h-60" x-data="barChart(@js($labels), @js($values))"><canvas x-ref="canvas"></canvas></div>
            </x-card>

            <x-card>
                <div class="mb-1 flex items-center gap-2">
                    <x-icon name="activity" class="size-5 text-sky-600" />
                    <h2 class="text-lg font-extrabold tracking-tight">Recent activity</h2>
                </div>
                @include('partials.activity', ['events' => $activity])
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Competitions</h2>
                    <a href="{{ route('admin.competitions.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">Manage</a>
                </div>
                <ul class="mt-2 divide-y divide-sky-50">
                    @foreach ($competitions as $competition)
                        <li class="flex items-center gap-2 py-2.5 text-sm">
                            <a href="{{ route('admin.competitions.edit', $competition) }}" class="min-w-0 grow truncate font-semibold hover:text-sky-700">{{ $competition->title }}</a>
                            <span class="text-xs text-slate-400">{{ $competition->challenges_count }} ch.</span>
                            <x-phase-badge :phase="$competition->phase" />
                        </li>
                    @endforeach
                </ul>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Top teams</h2>
                    <a href="{{ route('admin.teams.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">All teams</a>
                </div>
                <ol class="mt-3 space-y-2">
                    @foreach ($topTeams as $index => $team)
                        <li class="flex items-center gap-3 text-sm">
                            <span class="flex size-7 items-center justify-center rounded-full bg-sky-100 text-xs font-extrabold text-sky-800">{{ $index + 1 }}</span>
                            <a href="{{ route('teams.show', $team) }}" class="min-w-0 grow truncate font-semibold hover:text-sky-700">{{ $team->name }}</a>
                            <span class="text-xs text-slate-400">{{ $team->users_count }} members</span>
                            <span class="font-extrabold tabular-nums">{{ $team->solves_count }}</span>
                        </li>
                    @endforeach
                </ol>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Newest users</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">All users</a>
                </div>
                <ul class="mt-3 space-y-2.5">
                    @foreach ($latestUsers as $user)
                        <li class="flex items-center gap-3 text-sm">
                            <x-avatar :name="$user->name" size="sm" />
                            <div class="min-w-0 grow">
                                <div class="truncate font-semibold">{{ $user->name }}</div>
                                <div class="truncate text-xs text-slate-500">{{ $user->team?->name ?? 'No team' }}</div>
                            </div>
                            <span class="text-xs text-slate-400">{{ $user->created_at->diffForHumans(short: true) }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        </div>
    </div>
@endsection
