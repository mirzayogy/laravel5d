@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-page-header :title="'Hi, '.\Illuminate\Support\Str::before($user->name, ' ').'!'" subtitle="Here is where your team stands and what is happening around you." icon="grid">
        <x-slot:actions>
            <x-button :href="route('competitions.index')" variant="accent" icon="flag">Browse competitions</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($team && $current)
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat icon="trophy" tone="amber" label="Your rank" :value="$standing ? '#'.$standing['rank'] : '-'" :hint="$standing ? 'of '.$standing['total'].' teams' : ($current->isUpcoming() ? 'Starts soon' : 'Not registered')" />
            <x-stat icon="star" tone="coral" label="Score" :value="$standing ? number_format($standing['row']->score) : '0'" :hint="$standing ? $standing['row']->points.' points, -'.$standing['row']->hint_cost.' hints' : null" />
            <x-stat icon="check-circle" tone="green" label="Solved" :value="$progress['solved'].' / '.$progress['total']" hint="in this competition" />
            <x-stat icon="users" tone="sky" label="Team" :value="$team->name" :hint="$team->users->count().' of '.config('flagboard.team_size').' members'" />
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if ($current)
                <x-card class="relative overflow-hidden">
                    <div class="absolute -top-8 -right-8 size-32 rounded-full bg-coral-100"></div>
                    <div class="relative">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-phase-badge :phase="$current->phase" />
                            <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Your current competition</span>
                        </div>
                        <h2 class="mt-2 text-xl font-extrabold tracking-tight">{{ $current->title }}</h2>
                        @if ($progress)
                            <div class="mt-4">
                                <div class="mb-1 flex justify-between text-xs font-semibold text-slate-500">
                                    <span>Progress</span>
                                    <span>{{ $progress['solved'] }} of {{ $progress['total'] }} solved</span>
                                </div>
                                <x-progress :value="$progress['total'] ? $progress['solved'] / $progress['total'] * 100 : 0" tone="green" />
                            </div>
                        @endif
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            @if ($current->isRunning())
                                <span class="text-sm font-semibold text-slate-500">Ends in</span>
                                <x-countdown :target="$current->end_at" />
                            @elseif ($current->isUpcoming())
                                <span class="text-sm font-semibold text-slate-500">Starts in</span>
                                <x-countdown :target="$current->start_at" />
                            @endif
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2">
                            @if (! $current->isUpcoming())
                                <x-button :href="route('challenges.index', $current)" variant="accent" icon="target">Open challenges</x-button>
                            @endif
                            <x-button :href="route('scoreboard.show', $current)" variant="ghost" icon="trophy">Scoreboard</x-button>
                            <x-button :href="route('competitions.show', $current)" variant="soft" icon="info">Details</x-button>
                        </div>
                    </div>
                </x-card>
            @elseif ($team)
                <x-empty icon="flag" title="Your team has not joined a competition yet" text="Pick one below and register {{ $team->name }}. It only takes a click.">
                    <x-button :href="route('competitions.index')" variant="accent" icon="flag">See competitions</x-button>
                </x-empty>
            @else
                <x-card tone="sky" class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-3xl bg-sky-500 text-white shadow-[0_4px_0_0_var(--color-sky-700)]"><x-icon name="users" class="size-7" /></span>
                    <div class="grow">
                        <h2 class="text-lg font-extrabold">You are not in a team yet</h2>
                        <p class="text-sm text-slate-600">Create your own team or join friends with an invite code. Then you can register for competitions.</p>
                    </div>
                    <x-button :href="route('team.show')" icon="user-plus">Set up a team</x-button>
                </x-card>
            @endif

            @if ($open->isNotEmpty())
                <div>
                    <h2 class="mb-3 text-lg font-extrabold tracking-tight">Open for registration</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($open->take(2) as $competition)
                            @include('partials.competition-card', ['competition' => $competition])
                        @endforeach
                    </div>
                </div>
            @endif

            <x-card>
                <div class="mb-1 flex items-center gap-2">
                    <x-icon name="activity" class="size-5 text-sky-600" />
                    <h2 class="text-lg font-extrabold tracking-tight">{{ $team ? 'Team activity' : 'Activity' }}</h2>
                </div>
                @include('partials.activity', ['events' => $activity])
            </x-card>
        </div>

        <div class="space-y-6">
            @if ($team)
                <x-card>
                    <div class="flex items-center justify-between">
                        <h2 class="font-extrabold">Your team</h2>
                        <a href="{{ route('team.show') }}" class="text-sm font-semibold text-sky-700 hover:underline">Manage</a>
                    </div>
                    <div class="mt-3 flex -space-x-2">
                        @foreach ($team->users as $member)
                            <x-avatar :name="$member->name" class="ring-2 ring-white" />
                        @endforeach
                    </div>
                    <p class="mt-3 text-sm text-slate-600">Captain: <span class="font-semibold">{{ $team->captain?->name ?? 'nobody yet' }}</span></p>
                </x-card>
            @endif

            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Notifications</h2>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">See all</a>
                </div>
                <ul class="mt-2 divide-y divide-sky-50">
                    @forelse ($notifications as $notification)
                        <li>
                            <a href="{{ $notification->data['url'] ?? route('notifications.index') }}" class="flex gap-3 py-2.5 text-sm">
                                <x-icon :name="$notification->data['icon'] ?? 'bell'" class="mt-0.5 size-4 {{ $notification->read_at ? 'text-slate-300' : 'text-coral-500' }}" />
                                <span class="min-w-0">
                                    <span class="block truncate font-semibold {{ $notification->read_at ? 'text-slate-500' : '' }}">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                    <span class="block text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="py-4 text-center text-sm text-slate-500">You are all caught up.</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">My writeups</h2>
                    <a href="{{ route('writeups.index', ['mine' => 1]) }}" class="text-sm font-semibold text-sky-700 hover:underline">See all</a>
                </div>
                <ul class="mt-2 space-y-2">
                    @forelse ($writeups as $writeup)
                        <li class="flex items-center justify-between gap-2 text-sm">
                            <a href="{{ route('writeups.show', $writeup) }}" class="truncate font-semibold hover:text-sky-700">{{ $writeup->title }}</a>
                            <x-badge :tone="$writeup->status === 'published' ? 'green' : 'slate'">{{ $writeup->status }}</x-badge>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">Solve a challenge, then share how you did it.</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card>
                <h2 class="font-extrabold">Quick links</h2>
                <div class="mt-3 grid gap-2">
                    @foreach ([
                        [$team ? route('team.show') : route('team.show'), 'user-plus', $team ? 'Invite teammates' : 'Create or join a team', 'sky'],
                        [route('writeups.index'), 'file-text', 'Read writeups', 'violet'],
                        [route('teams.index'), 'users', 'See all teams', 'green'],
                        [route('profile.edit'), 'user', 'Edit my profile', 'amber'],
                    ] as [$url, $icon, $label, $tone])
                        <a href="{{ $url }}" class="flex items-center gap-3 rounded-2xl bg-sky-50/70 px-3 py-2.5 text-sm font-semibold hover:bg-sky-100">
                            <x-icon :name="$icon" class="size-4 text-sky-600" /> {{ $label }}
                            <x-icon name="arrow-right" class="ml-auto size-4 text-slate-400" />
                        </a>
                    @endforeach
                </div>
            </x-card>

            @unless ($user->hasVerifiedEmail())
                <x-card tone="coral">
                    <div class="flex gap-3">
                        <x-icon name="mail" class="mt-0.5 size-5 text-coral-700" />
                        <div class="text-sm">
                            <div class="font-bold">Verify your email</div>
                            <p class="text-slate-600">Register teams and submit flags after verifying.</p>
                            <a href="{{ route('verification.notice') }}" class="mt-1 inline-block font-bold text-coral-700 underline">Verify now</a>
                        </div>
                    </div>
                </x-card>
            @endunless
        </div>
    </div>
@endsection
