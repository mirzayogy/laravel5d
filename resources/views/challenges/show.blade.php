@extends('layouts.app')

@section('title', $challenge->title)

@section('content')
    <x-page-header :title="$challenge->title" icon="target"
                   :crumbs="['Competitions' => route('competitions.index'), $competition->title => route('competitions.show', $competition), 'Challenges' => route('challenges.index', $competition), $challenge->title => null]">
        <x-slot:actions>
            <div class="rounded-2xl bg-coral-500 px-4 py-2 text-center text-white shadow-[0_4px_0_0_var(--color-coral-700)]">
                <div class="text-2xl leading-none font-extrabold tabular-nums">{{ $challenge->points }}</div>
                <div class="text-[10px] font-bold tracking-wide uppercase opacity-90">points</div>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-5 flex flex-wrap items-center gap-2">
        <x-category-badge :name="$challenge->category->name" />
        <x-difficulty :level="$challenge->difficulty" />
        @foreach ($challenge->tags as $tag)
            <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-xs font-semibold text-slate-600">#{{ $tag->name }}</span>
        @endforeach
        @unless ($challenge->is_published)<x-badge tone="amber">Not published</x-badge>@endunless
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <h2 class="mb-2 flex items-center gap-2 text-lg font-extrabold tracking-tight"><x-icon name="file-text" class="size-5 text-sky-600" /> Description</h2>
                <div class="md">{!! $descriptionHtml !!}</div>
            </x-card>

            @if ($solved)
                <x-card tone="green" class="flex flex-wrap items-center gap-4">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-emerald-500 text-white"><x-icon name="check-circle" class="size-6" /></span>
                    <div class="grow">
                        <h2 class="font-extrabold">Your team solved this challenge</h2>
                        <p class="text-sm text-slate-600">Nice work! Share how you did it with everyone.</p>
                    </div>
                    <x-button :href="route('writeups.create', $challenge)" variant="accent" icon="edit">Write a writeup</x-button>
                </x-card>
            @elseif (! $team)
                <x-card tone="sky" class="text-sm text-slate-600">You are previewing this challenge as an admin. Only registered teams can submit flags.</x-card>
            @elseif ($competition->isRunning())
                <x-card tone="coral">
                    <h2 class="mb-3 flex items-center gap-2 text-lg font-extrabold tracking-tight"><x-icon name="flag" class="size-5 text-coral-600" /> Submit the flag</h2>
                    <form method="POST" action="{{ route('challenges.submit', $challenge) }}" class="flex flex-wrap items-start gap-3">
                        @csrf
                        <div class="min-w-56 grow">
                            <input name="flag" type="text" required autocomplete="off" placeholder="FLAG{...}" aria-label="Flag"
                                   class="w-full rounded-2xl border-2 border-coral-200 bg-white px-4 py-3 font-mono text-sm placeholder:text-slate-400 focus:border-coral-500 focus:outline-none">
                            @error('flag')<p class="mt-1 text-xs font-medium text-coral-700">{{ $message }}</p>@enderror
                        </div>
                        <x-button type="submit" variant="accent" size="lg" icon="send">Submit</x-button>
                    </form>
                    @if ($failedAttempts > 0)
                        <p class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-slate-500"><x-icon name="alert" class="size-3.5" /> Wrong attempts by your team: {{ $failedAttempts }}</p>
                    @endif
                </x-card>
            @else
                <x-card tone="sky" class="text-sm text-slate-600">This competition is not running, so submissions are closed.</x-card>
            @endif

            <x-card>
                <h2 class="mb-3 flex items-center gap-2 text-lg font-extrabold tracking-tight"><x-icon name="bulb" class="size-5 text-amber-500" /> Hints</h2>
                <div class="space-y-3">
                    @forelse ($challenge->hints as $index => $hint)
                        <div class="rounded-2xl border-2 p-4 {{ in_array($hint->id, $unlockedHintIds, true) ? 'border-amber-200 bg-amber-50' : 'border-sky-100 bg-sky-50/50' }}">
                            @if (in_array($hint->id, $unlockedHintIds, true) || ($team === null))
                                <div class="mb-1 flex items-center gap-2 text-xs font-bold tracking-wide text-amber-700 uppercase"><x-icon name="unlock" class="size-3.5" /> Hint {{ $index + 1 }}@if ($team === null) (costs {{ $hint->cost }})@endif</div>
                                <p class="text-sm whitespace-pre-line text-slate-700">{{ $hint->content }}</p>
                            @else
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 text-sm">
                                        <span class="flex size-9 items-center justify-center rounded-full bg-white text-slate-400"><x-icon name="lock" class="size-4" /></span>
                                        <div>
                                            <div class="font-bold">Hint {{ $index + 1 }} is locked</div>
                                            <div class="text-xs text-slate-500">Unlocking costs your team {{ $hint->cost }} points.</div>
                                        </div>
                                    </div>
                                    @if (! $solved && $competition->isRunning())
                                        <form method="POST" action="{{ route('hints.unlock', $hint) }}" onsubmit="return confirm('Unlock this hint for {{ $hint->cost }} points?')">
                                            @csrf
                                            <x-button type="submit" size="sm" variant="ghost" icon="unlock">Unlock</x-button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">This challenge has no hints. You are on your own.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Solves</h2>
                    <x-badge tone="green">{{ $solves->count() }} {{ \Illuminate\Support\Str::plural('team', $solves->count()) }}</x-badge>
                </div>
                @if ($solves->isNotEmpty())
                    <div class="mt-3 rounded-2xl bg-coral-50 px-3 py-2.5 text-sm">
                        <div class="flex items-center gap-1.5 text-xs font-bold tracking-wide text-coral-700 uppercase"><x-icon name="zap" class="size-3.5" /> First blood</div>
                        <a href="{{ route('teams.show', $solves->first()->team) }}" class="font-bold hover:text-sky-700">{{ $solves->first()->team->name }}</a>
                        <span class="text-xs text-slate-500">{{ $solves->first()->submitted_at->diffForHumans() }}</span>
                    </div>
                    <ul class="mt-3 space-y-2">
                        @foreach ($solves->skip(1)->take(6) as $solve)
                            <li class="flex items-center gap-2 text-sm">
                                <x-avatar :name="$solve->team->name" size="sm" />
                                <a href="{{ route('teams.show', $solve->team) }}" class="min-w-0 grow truncate font-semibold hover:text-sky-700">{{ $solve->team->name }}</a>
                                <span class="text-xs text-slate-400">{{ $solve->submitted_at->diffForHumans(short: true) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-slate-500">Nobody has solved this yet. Be the first!</p>
                @endif
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Writeups</h2>
                    <a href="{{ route('writeups.index', ['competition' => $competition->id]) }}" class="text-sm font-semibold text-sky-700 hover:underline">Browse</a>
                </div>
                <ul class="mt-2 divide-y divide-sky-50">
                    @forelse ($writeups as $writeup)
                        <li class="py-2 text-sm">
                            <a href="{{ route('writeups.show', $writeup) }}" class="font-semibold hover:text-sky-700">{{ $writeup->title }}</a>
                            <div class="text-xs text-slate-500">by {{ $writeup->user->name }}</div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-slate-500">No writeup yet.</li>
                    @endforelse
                </ul>
            </x-card>

            <x-card tone="sky">
                <h2 class="font-extrabold">{{ $competition->title }}</h2>
                <div class="mt-2 flex items-center gap-2 text-sm">
                    <x-phase-badge :phase="$competition->phase" />
                    @if ($competition->isRunning())<x-countdown :target="$competition->end_at" />@endif
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <x-button :href="route('challenges.index', $competition)" size="sm" variant="ghost" icon="arrow-left">All challenges</x-button>
                    <x-button :href="route('scoreboard.show', $competition)" size="sm" variant="soft" icon="trophy">Scoreboard</x-button>
                </div>
            </x-card>
        </div>
    </div>
@endsection
