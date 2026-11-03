@extends('layouts.app')

@section('title', 'Home')

@section('content')
    {{-- Hero --}}
    <section class="grid items-center gap-8 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <x-badge tone="coral" class="text-sm"><x-icon name="zap" class="size-3.5" /> Capture The Flag, made friendly</x-badge>
            <h1 class="mt-4 text-4xl leading-[1.1] font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                Solve puzzles.<br>
                <span class="bg-gradient-to-t from-coral-200 from-40% to-transparent to-40% px-1">Climb the board.</span>
            </h1>
            <p class="mt-4 max-w-xl text-base text-slate-600">
                Flagboard is the home of friendly hacking competitions. Form a team, crack challenges in six categories, unlock hints when you are stuck, and watch the scoreboard move in real time.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <x-button :href="route('competitions.index')" size="lg" variant="accent" icon="flag">Browse competitions</x-button>
                @auth
                    <x-button :href="route('dashboard')" size="lg" variant="ghost" icon="grid">Open my dashboard</x-button>
                @else
                    <x-button :href="route('register')" size="lg" variant="ghost" icon="user-plus">Create an account</x-button>
                @endauth
            </div>
            <div class="mt-8 grid max-w-xl grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ([['Teams', $stats['teams'], 'sky'], ['Players', $stats['players'], 'coral'], ['Challenges', $stats['challenges'], 'green'], ['Solves', $stats['solves'], 'amber']] as [$label, $value, $tone])
                    <div class="rounded-2xl border-2 border-white bg-white/70 px-3 py-2 text-center shadow-sm">
                        <div class="text-2xl font-extrabold tracking-tight text-slate-900 tabular-nums">{{ number_format($value) }}</div>
                        <div class="text-[11px] font-semibold tracking-wide text-slate-500 uppercase">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative lg:col-span-2">
            <div class="absolute -top-4 -right-2 z-10 rotate-6 rounded-2xl bg-coral-500 px-3 py-1.5 text-sm font-extrabold text-white shadow-[0_4px_0_0_var(--color-coral-700)]">Live now</div>
            <x-card class="rotate-1 transition hover:rotate-0">
                @if ($featured)
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Scoreboard</div>
                            <a href="{{ route('scoreboard.show', $featured) }}" class="block truncate text-lg font-extrabold hover:text-sky-700">{{ $featured->title }}</a>
                        </div>
                        <x-icon name="trophy" class="size-8 text-amber-400" />
                    </div>
                    <div class="mt-4">@include('partials.mini-ranking', ['rows' => $topTeams])</div>
                    <div class="mt-4 flex items-center justify-between gap-2 border-t border-sky-100 pt-3">
                        <span class="text-xs font-semibold text-slate-500">Ends in</span>
                        <x-countdown :target="$featured->end_at" />
                    </div>
                @else
                    <x-empty icon="flag" title="No competition is live" text="The next one will show up here as soon as it starts." />
                @endif
            </x-card>
        </div>
    </section>

    {{-- Competitions --}}
    <section class="mt-14">
        <div class="mb-4 flex items-end justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight">Happening now and next</h2>
                <p class="text-sm text-slate-500">Pick a competition, register your team, and get started.</p>
            </div>
            <x-button :href="route('competitions.index')" variant="soft" size="sm" icon="arrow-right">All competitions</x-button>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($cards as $competition)
                @include('partials.competition-card', ['competition' => $competition])
            @endforeach
            @if ($cards->isEmpty())
                <div class="md:col-span-2 lg:col-span-3">
                    <x-empty icon="calendar" title="Nothing scheduled yet" text="Admins are preparing the next competition." />
                </div>
            @endif
        </div>
    </section>

    {{-- Activity and steps --}}
    <section class="mt-14 grid gap-6 lg:grid-cols-5">
        <x-card class="lg:col-span-3">
            <div class="mb-1 flex items-center gap-2">
                <x-icon name="activity" class="size-5 text-sky-600" />
                <h2 class="text-lg font-extrabold tracking-tight">Recent activity</h2>
            </div>
            @include('partials.activity', ['events' => $activity])
        </x-card>

        <div id="how-it-works" class="grid gap-4 lg:col-span-2">
            @foreach ([
                ['user-plus', 'Create your team', 'Sign up, create a team, and share the invite code with up to '.config('flagboard.team_size').' players.', 'sky'],
                ['key', 'Capture the flags', 'Open challenges, submit flags, and unlock hints for a few points when you need a nudge.', 'coral'],
                ['trophy', 'Climb the board', 'Every solve earns points. The scoreboard updates live, and first blood gets a badge.', 'amber'],
            ] as $index => [$icon, $title, $text, $tone])
                <x-card class="flex items-center gap-4">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl {{ ['sky' => 'bg-sky-100 text-sky-700', 'coral' => 'bg-coral-100 text-coral-700', 'amber' => 'bg-amber-100 text-amber-700'][$tone] }}">
                        <x-icon :name="$icon" class="size-6" />
                    </span>
                    <div>
                        <div class="text-xs font-bold tracking-wide text-slate-400 uppercase">Step {{ $index + 1 }}</div>
                        <h3 class="font-extrabold">{{ $title }}</h3>
                        <p class="mt-0.5 text-sm text-slate-600">{{ $text }}</p>
                    </div>
                </x-card>
            @endforeach
        </div>
    </section>

    @guest
        <section class="mt-14">
            <x-card tone="coral" class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                <span class="flex size-16 shrink-0 animate-float items-center justify-center rounded-3xl bg-coral-500 text-white shadow-[0_5px_0_0_var(--color-coral-700)]"><x-icon name="flag" class="size-8" /></span>
                <div class="grow">
                    <h2 class="text-xl font-extrabold tracking-tight">Ready to capture your first flag?</h2>
                    <p class="text-sm text-slate-600">Creating an account takes less than a minute.</p>
                </div>
                <x-button :href="route('register')" variant="accent" size="lg">Join now</x-button>
            </x-card>
        </section>
    @endguest
@endsection
