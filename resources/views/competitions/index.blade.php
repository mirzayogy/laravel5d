@extends('layouts.app')

@section('title', 'Competitions')

@section('content')
    <x-page-header title="Competitions" subtitle="Live, upcoming, and finished. Pick one and bring your team." icon="flag" />

    <x-card class="mb-6">
        <form method="GET" action="{{ route('competitions.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-56 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search competitions" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach (['' => ['All', $counts['all']], 'running' => ['Live', $counts['running']], 'upcoming' => ['Upcoming', $counts['upcoming']], 'finished' => ['Finished', $counts['finished']]] as $value => [$label, $count])
                    <a href="{{ route('competitions.index', array_filter(['q' => $search, 'phase' => $value])) }}"
                       class="flex items-center gap-1.5 rounded-full border-2 px-3.5 py-1.5 text-sm font-semibold transition {{ ($phase ?? '') === $value ? 'border-sky-500 bg-sky-500 text-white' : 'border-sky-100 bg-white text-slate-600 hover:border-sky-300' }}">
                        {{ $label }}
                        <span class="rounded-full px-1.5 text-xs {{ ($phase ?? '') === $value ? 'bg-white/25' : 'bg-sky-100 text-sky-800' }}">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
            <x-button type="submit" icon="search">Search</x-button>
        </form>
    </x-card>

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($competitions as $competition)
            @include('partials.competition-card', ['competition' => $competition, 'registered' => in_array($competition->id, $registeredIds, true)])
        @empty
            <div class="md:col-span-2 lg:col-span-3">
                <x-empty icon="search" title="No competition found" text="Try a different search or clear the filter.">
                    <x-button :href="route('competitions.index')" variant="ghost">Clear filters</x-button>
                </x-empty>
            </div>
        @endforelse
    </div>
@endsection
