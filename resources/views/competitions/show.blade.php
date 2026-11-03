@extends('layouts.app')

@section('title', $competition->title)

@section('content')
    @php($user = auth()->user())

    <x-page-header :title="$competition->title" icon="flag" :crumbs="['Competitions' => route('competitions.index'), $competition->title => null]">
        <x-slot:actions>
            <x-phase-badge :phase="$competition->phase" class="text-sm" />
        </x-slot:actions>
    </x-page-header>

    <x-card tone="sky" class="relative mb-6 overflow-hidden">
        <div class="absolute -right-10 -bottom-12 size-40 rounded-full bg-white/60"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-1 text-sm">
                <div class="flex items-center gap-2 font-semibold"><x-icon name="calendar" class="size-4 text-sky-600" /> {{ $competition->start_at->format('D, d M Y H:i') }} to {{ $competition->end_at->format('D, d M Y H:i') }}</div>
                <div class="flex items-center gap-2 text-slate-600"><x-icon name="clock" class="size-4 text-sky-600" /> {{ $competition->start_at->diffInHours($competition->end_at) }} hours long</div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if ($competition->isRunning())
                    <span class="text-sm font-semibold text-slate-600">Ends in</span><x-countdown :target="$competition->end_at" />
                @elseif ($competition->isUpcoming())
                    <span class="text-sm font-semibold text-slate-600">Starts in</span><x-countdown :target="$competition->start_at" />
                @else
                    <span class="text-sm font-semibold text-slate-600">Ended {{ $competition->end_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
        <div class="relative mt-4 flex flex-wrap gap-2">
            @auth
                @if ($participating)
                    @if (! $competition->isUpcoming())
                        <x-button :href="route('challenges.index', $competition)" variant="accent" icon="target">Open challenges</x-button>
                    @endif
                    <x-badge tone="green" class="px-3 py-2 text-sm"><x-icon name="check" class="size-4" /> {{ $team->name }} is registered</x-badge>
                @elseif (! $team)
                    <x-button :href="route('team.show')" icon="users">Create or join a team to take part</x-button>
                @elseif (! $competition->isFinished())
                    <form method="POST" action="{{ route('competitions.register', $competition) }}">
                        @csrf
                        <x-button type="submit" variant="accent" icon="flag">Register {{ $team->name }}</x-button>
                    </form>
                @else
                    <x-badge tone="slate" class="px-3 py-2 text-sm">Registration is closed</x-badge>
                @endif
                @if ($user->isAdmin() && ! $competition->isUpcoming())
                    <x-button :href="route('challenges.index', $competition)" variant="ghost" icon="eye">Preview challenges</x-button>
                @endif
            @else
                <x-button :href="route('login')" icon="key">Log in to take part</x-button>
            @endauth
            @unless ($competition->isUpcoming())
                <x-button :href="route('scoreboard.show', $competition)" variant="ghost" icon="trophy">Scoreboard</x-button>
            @endunless
        </div>
    </x-card>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat icon="flag" tone="coral" label="Challenges" :value="$stats['challenges']" :hint="number_format($stats['points']).' points to win'" />
        <x-stat icon="users" tone="sky" label="Teams" :value="$stats['teams']" hint="registered so far" />
        <x-stat icon="check-circle" tone="green" label="Solves" :value="$stats['solves']" hint="correct submissions" />
        <x-stat icon="target" tone="amber" label="Attempts" :value="$stats['attempts']" :hint="$stats['attempts'] ? round($stats['solves'] / $stats['attempts'] * 100).'% success rate' : 'none yet'" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="text-lg font-extrabold tracking-tight">About this competition</h2>
                <div class="md mt-2">{!! \Illuminate\Support\Str::markdown($competition->description ?? 'No description yet.', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-extrabold tracking-tight">Categories</h2>
                    @if ($participating)<span class="text-xs font-semibold text-slate-500">Your team's progress</span>@endif
                </div>
                <div class="mt-3 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    @forelse ($categories as $name => $row)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <x-category-badge :name="$name" />
                                <span class="font-semibold text-slate-600">{{ $participating ? $row['solved'].' / ' : '' }}{{ $row['total'] }}</span>
                            </div>
                            <x-progress :value="$row['total'] ? $row['solved'] / $row['total'] * 100 : 0" tone="green" />
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 sm:col-span-2">No challenge is published yet.</p>
                    @endforelse
                </div>
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
                    <h2 class="font-extrabold">Top teams</h2>
                    <a href="{{ route('scoreboard.show', $competition) }}" class="text-sm font-semibold text-sky-700 hover:underline">Full board</a>
                </div>
                <div class="mt-3">@include('partials.mini-ranking', ['rows' => $topTeams, 'myTeamId' => $team?->id])</div>
                @if ($myStanding && $myStanding['rank'] > 5)
                    <div class="mt-3 rounded-2xl bg-coral-50 px-3 py-2 text-sm font-semibold text-coral-800">You are #{{ $myStanding['rank'] }} with {{ $myStanding['row']->score }} points.</div>
                @endif
            </x-card>

            <x-card>
                <h2 class="font-extrabold">Difficulty mix</h2>
                <div class="mt-3 space-y-3">
                    @foreach (['easy' => ['Easy', 'green'], 'medium' => ['Medium', 'amber'], 'hard' => ['Hard', 'coral']] as $level => [$label, $tone])
                        <div>
                            <div class="mb-1 flex justify-between text-sm font-semibold"><span>{{ $label }}</span><span class="text-slate-500">{{ $difficulty[$level] ?? 0 }}</span></div>
                            <x-progress :value="$stats['challenges'] ? ($difficulty[$level] ?? 0) / $stats['challenges'] * 100 : 0" :tone="$tone" />
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card>
                <h2 class="flex items-center gap-2 font-extrabold"><x-icon name="zap" class="size-4 text-coral-500" /> Latest first bloods</h2>
                <ul class="mt-2 divide-y divide-sky-50">
                    @forelse ($bloods as $blood)
                        <li class="py-2 text-sm">
                            <div class="truncate font-semibold">{{ $titles[$blood->challenge_id] ?? 'Challenge' }}</div>
                            <div class="text-xs text-slate-500">{{ $blood->team }}, {{ \Illuminate\Support\Carbon::parse($blood->at)->diffForHumans() }}</div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-slate-500">No first blood yet.</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card tone="coral">
                <h2 class="font-extrabold">How scoring works</h2>
                <ul class="mt-2 space-y-2 text-sm text-slate-700">
                    <li class="flex gap-2"><x-icon name="check-circle" class="mt-0.5 size-4 text-emerald-600" /> Every solved challenge adds its points once per team.</li>
                    <li class="flex gap-2"><x-icon name="bulb" class="mt-0.5 size-4 text-amber-600" /> Hints cost points, so use them wisely.</li>
                    <li class="flex gap-2"><x-icon name="zap" class="mt-0.5 size-4 text-coral-600" /> The first team to solve a challenge earns a first blood badge.</li>
                    <li class="flex gap-2"><x-icon name="clock" class="mt-0.5 size-4 text-sky-600" /> Ties go to the team that finished earlier.</li>
                </ul>
            </x-card>
        </div>
    </div>
@endsection
