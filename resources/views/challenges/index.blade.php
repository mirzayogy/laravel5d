@extends('layouts.app')

@section('title', 'Challenges')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $competition->title }}: challenges</h1>
        <a href="{{ route('scoreboard.show', $competition) }}" class="rounded border border-slate-300 px-3 py-1 text-sm hover:bg-slate-100">Scoreboard</a>
    </div>

    @forelse ($challenges->groupBy(fn ($challenge) => $challenge->category->name) as $category => $items)
        <h2 class="mt-6 text-lg font-semibold">{{ $category }}</h2>
        <div class="mt-2 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $challenge)
                @php($solved = in_array($challenge->id, $solvedIds, true))
                <a href="{{ route('challenges.show', $challenge) }}"
                   class="block rounded border p-3 {{ $solved ? 'border-green-300 bg-green-50' : 'border-slate-200 bg-white hover:border-indigo-300' }}">
                    <div class="font-medium">{{ $challenge->title }}</div>
                    <div class="mt-1 flex items-center justify-between text-sm text-slate-500">
                        <span>{{ $challenge->points }} points, {{ $challenge->difficulty }}</span>
                        @if ($solved)
                            <span class="text-green-700">Solved</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @empty
        <p class="mt-6 text-sm text-slate-500">No challenge is published yet.</p>
    @endforelse
@endsection
