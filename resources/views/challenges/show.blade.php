@extends('layouts.app')

@section('title', $challenge->title)

@section('content')
    <a href="{{ route('challenges.index', $competition) }}" class="text-sm text-indigo-700 underline">Back to challenges</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $challenge->title }}</h1>
        <span class="text-sm text-slate-500">{{ $challenge->points }} points</span>
    </div>
    <p class="mt-1 text-sm text-slate-500">
        {{ $challenge->category->name }}, {{ $challenge->difficulty }}
        @foreach ($challenge->tags as $tag)
            <span class="ml-1 rounded bg-slate-200 px-1.5 py-0.5 text-xs">{{ $tag->name }}</span>
        @endforeach
    </p>

    <p class="mt-4 whitespace-pre-line">{{ $challenge->description }}</p>

    <div class="mt-6 rounded border border-slate-200 bg-white p-4">
        @if ($solved)
            <p class="text-green-700">Your team solved this challenge.</p>
            <a href="{{ route('writeups.create', $challenge) }}" class="mt-2 inline-block text-sm text-indigo-700 underline">Write a writeup</a>
        @elseif ($competition->status === 'running')
            <form method="POST" action="{{ route('challenges.submit', $challenge) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="grow">
                    <label for="flag" class="block text-sm font-medium">Flag</label>
                    <input id="flag" name="flag" type="text" required autocomplete="off" placeholder="FLAG{...}"
                           class="mt-1 w-full rounded border border-slate-300 px-3 py-2 font-mono">
                </div>
                <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Submit</button>
            </form>
            @if ($failedAttempts > 0)
                <p class="mt-2 text-xs text-slate-500">Wrong attempts by your team: {{ $failedAttempts }}</p>
            @endif
        @else
            <p class="text-sm text-slate-500">This competition is not running, so submissions are closed.</p>
        @endif
    </div>

    <h2 class="mt-6 text-lg font-semibold">Hints</h2>
    <div class="mt-2 space-y-3">
        @forelse ($challenge->hints as $hint)
            <div class="rounded border border-slate-200 bg-white p-3 text-sm">
                @if (in_array($hint->id, $unlockedHintIds, true))
                    <p class="whitespace-pre-line">{{ $hint->content }}</p>
                @else
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Locked hint (costs {{ $hint->cost }} points)</span>
                        @if (! $solved && $competition->status === 'running')
                            <form method="POST" action="{{ route('hints.unlock', $hint) }}">
                                @csrf
                                <button type="submit" class="rounded border border-slate-300 px-3 py-1 hover:bg-slate-100">Unlock</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-500">This challenge has no hints.</p>
        @endforelse
    </div>
@endsection
