@extends('layouts.app')

@section('title', 'Competitions')

@section('content')
    <h1 class="text-2xl font-semibold">Competitions</h1>

    <div class="mt-4 space-y-3">
        @forelse ($competitions as $competition)
            <a href="{{ route('competitions.show', $competition) }}" class="block rounded border border-slate-200 bg-white p-4 hover:border-indigo-300">
                <div class="flex items-center justify-between">
                    <span class="font-medium">{{ $competition->title }}</span>
                    <span class="rounded-full px-2 py-0.5 text-xs {{ $competition->status === 'running' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">{{ $competition->status }}</span>
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    {{ $competition->start_at->format('d M Y H:i') }} to {{ $competition->end_at->format('d M Y H:i') }},
                    {{ $competition->challenges_count }} challenges, {{ $competition->teams_count }} teams
                </div>
            </a>
        @empty
            <p class="text-sm text-slate-500">There are no competitions yet.</p>
        @endforelse
    </div>
@endsection
