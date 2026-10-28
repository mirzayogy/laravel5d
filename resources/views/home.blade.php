@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h1 class="text-2xl font-semibold">Flagboard</h1>
    <p class="mt-1 text-slate-600">Capture The Flag competitions: form a team, solve challenges, climb the scoreboard.</p>

    <h2 class="mt-8 text-lg font-semibold">Running competitions</h2>
    <div class="mt-3 grid gap-4 sm:grid-cols-2">
        @forelse ($competitions as $competition)
            <a href="{{ route('competitions.show', $competition) }}" class="block rounded border border-slate-200 bg-white p-4 hover:border-indigo-300">
                <div class="font-medium">{{ $competition->title }}</div>
                <div class="mt-1 text-sm text-slate-500">
                    {{ $competition->challenges_count }} challenges, {{ $competition->teams_count }} teams
                </div>
                <div class="mt-1 text-xs text-slate-400">Ends {{ $competition->end_at->format('d M Y H:i') }}</div>
            </a>
        @empty
            <p class="text-sm text-slate-500">No competition is running right now.</p>
        @endforelse
    </div>
@endsection
