@extends('layouts.app')

@section('title', $competition->title)

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $competition->title }}</h1>
        <span class="rounded-full px-2 py-0.5 text-xs {{ $competition->status === 'running' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">{{ $competition->status }}</span>
    </div>
    <p class="mt-1 text-sm text-slate-500">
        {{ $competition->start_at->format('d M Y H:i') }} to {{ $competition->end_at->format('d M Y H:i') }},
        {{ $competition->challenges_count }} challenges, {{ $competition->teams_count }} teams
    </p>

    @if ($competition->description)
        <p class="mt-4 whitespace-pre-line">{{ $competition->description }}</p>
    @endif

    <div class="mt-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('scoreboard.show', $competition) }}" class="rounded border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100">Scoreboard</a>

        @auth
            @if ($registered)
                <a href="{{ route('challenges.index', $competition) }}" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Open challenges</a>
                <span class="text-sm text-green-700">Your team is registered.</span>
            @elseif (! $team)
                <span class="text-sm text-slate-500">
                    <a href="{{ route('team.show') }}" class="text-indigo-700 underline">Create or join a team</a> to take part.
                </span>
            @elseif ($competition->status === 'running')
                <form method="POST" action="{{ route('competitions.register', $competition) }}">
                    @csrf
                    <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Register {{ $team->name }}</button>
                </form>
            @else
                <span class="text-sm text-slate-500">Registration is closed.</span>
            @endif
        @else
            <span class="text-sm text-slate-500"><a href="{{ route('login') }}" class="text-indigo-700 underline">Log in</a> to take part.</span>
        @endauth
    </div>
@endsection
