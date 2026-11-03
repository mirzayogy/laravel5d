@extends('layouts.app')

@section('title', 'Challenges')

@section('content')
    <x-page-header :title="$competition->title" subtitle="Pick a challenge, find the flag, and submit it before time runs out." icon="target"
                   :crumbs="['Competitions' => route('competitions.index'), $competition->title => route('competitions.show', $competition), 'Challenges' => null]">
        <x-slot:actions>
            @if ($competition->isRunning())
                <x-countdown :target="$competition->end_at" />
            @endif
            <x-button :href="route('scoreboard.show', $competition)" variant="ghost" icon="trophy">Scoreboard</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-card>
            <div class="flex items-center justify-between text-sm font-semibold">
                <span class="flex items-center gap-2"><x-icon name="check-circle" class="size-4 text-emerald-600" /> Solved</span>
                <span>{{ $progress['solved'] }} / {{ $progress['total'] }}</span>
            </div>
            <x-progress class="mt-2" :value="$progress['total'] ? $progress['solved'] / $progress['total'] * 100 : 0" tone="green" />
        </x-card>
        <x-card>
            <div class="flex items-center justify-between text-sm font-semibold">
                <span class="flex items-center gap-2"><x-icon name="star" class="size-4 text-coral-500" /> Points earned</span>
                <span>{{ number_format($progress['earned']) }} / {{ number_format($progress['available']) }}</span>
            </div>
            <x-progress class="mt-2" :value="$progress['available'] ? $progress['earned'] / $progress['available'] * 100 : 0" tone="coral" />
        </x-card>
        <x-card class="flex items-center gap-3">
            <span class="flex size-11 items-center justify-center rounded-2xl bg-sky-100 text-sky-700"><x-icon name="users" class="size-5" /></span>
            <div class="min-w-0">
                <div class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Playing as</div>
                <div class="truncate font-extrabold">{{ $team?->name ?? 'Admin preview' }}</div>
            </div>
        </x-card>
    </div>

    <x-card class="mt-6">
        <form method="GET" action="{{ route('challenges.index', $competition) }}" class="space-y-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-56 grow">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $filters['search'] }}" placeholder="Search challenges" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
                </div>
                <select name="difficulty" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                    <option value="">Any difficulty</option>
                    @foreach (['easy', 'medium', 'hard'] as $level)
                        <option value="{{ $level }}" @selected($filters['difficulty'] === $level)>{{ ucfirst($level) }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                    <option value="">Solved and unsolved</option>
                    <option value="unsolved" @selected($filters['status'] === 'unsolved')>Unsolved</option>
                    <option value="solved" @selected($filters['status'] === 'solved')>Solved</option>
                </select>
                @if ($filters['categoryId'])<input type="hidden" name="category" value="{{ $filters['categoryId'] }}">@endif
                <x-button type="submit" icon="search">Filter</x-button>
            </div>
            <div class="flex flex-wrap gap-1.5">
                <a href="{{ request()->fullUrlWithoutQuery('category') }}" class="rounded-full border-2 px-3 py-1 text-sm font-semibold {{ $filters['categoryId'] ? 'border-sky-100 bg-white text-slate-600 hover:border-sky-300' : 'border-sky-500 bg-sky-500 text-white' }}">All categories <span class="opacity-70">{{ $total }}</span></a>
                @foreach ($categories as $category)
                    <a href="{{ request()->fullUrlWithQuery(['category' => $category->id]) }}" class="rounded-full border-2 px-3 py-1 text-sm font-semibold {{ (int) $filters['categoryId'] === $category->id ? 'border-sky-500 bg-sky-500 text-white' : 'border-sky-100 bg-white text-slate-600 hover:border-sky-300' }}">{{ $category->name }} <span class="opacity-70">{{ $categoryCounts[$category->id] ?? 0 }}</span></a>
                @endforeach
            </div>
        </form>
    </x-card>

    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($challenges as $challenge)
            @php($solved = in_array($challenge->id, $solvedIds, true))
            <x-card :tone="$solved ? 'green' : 'white'" :hover="true" class="relative flex flex-col">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex flex-wrap gap-1.5">
                        <x-category-badge :name="$challenge->category->name" />
                        <x-difficulty :level="$challenge->difficulty" />
                    </div>
                    <div class="text-right">
                        <div class="text-2xl leading-none font-extrabold tracking-tight text-coral-600 tabular-nums">{{ $challenge->points }}</div>
                        <div class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">points</div>
                    </div>
                </div>
                <a href="{{ route('challenges.show', $challenge) }}" class="mt-3 text-lg leading-tight font-extrabold tracking-tight text-slate-900 after:absolute after:inset-0 after:rounded-3xl hover:text-sky-700">{{ $challenge->title }}</a>
                <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags(\Illuminate\Support\Str::markdown($challenge->description)), ENT_QUOTES), 90) }}</p>
                <div class="mt-3 flex flex-wrap gap-1">
                    @foreach ($challenge->tags as $tag)
                        <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[11px] font-semibold text-slate-600">#{{ $tag->name }}</span>
                    @endforeach
                </div>
                <div class="mt-auto flex items-center justify-between gap-2 pt-4 text-xs font-semibold text-slate-500">
                    <span class="flex items-center gap-1"><x-icon name="users" class="size-3.5" /> {{ $solveCounts[$challenge->id] ?? 0 }} solves</span>
                    @if ($solved)
                        <span class="flex items-center gap-1 text-emerald-700"><x-icon name="check-circle" class="size-4" /> Solved</span>
                    @elseif (isset($firstBloods[$challenge->id]))
                        <span class="flex items-center gap-1 text-coral-600"><x-icon name="zap" class="size-3.5" /> {{ \Illuminate\Support\Str::limit($firstBloods[$challenge->id]->team, 16) }}</span>
                    @else
                        <span class="text-slate-400">No solves yet</span>
                    @endif
                </div>
            </x-card>
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                <x-empty icon="search" title="No challenge matches" text="Change the filters to see more challenges.">
                    <x-button :href="route('challenges.index', $competition)" variant="ghost">Clear filters</x-button>
                </x-empty>
            </div>
        @endforelse
    </div>
@endsection
