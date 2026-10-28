@extends('layouts.app')

@section('title', 'Writeups')

@section('content')
    <h1 class="text-2xl font-semibold">Writeups</h1>

    @auth
        <h2 class="mt-6 text-lg font-semibold">My writeups</h2>
        <div class="mt-2 space-y-2">
            @forelse ($mine as $writeup)
                <div class="flex items-center justify-between rounded border border-slate-200 bg-white p-3 text-sm">
                    <div>
                        <a href="{{ route('writeups.show', $writeup) }}" class="font-medium text-indigo-700 underline">{{ $writeup->title }}</a>
                        <span class="text-slate-500">on {{ $writeup->challenge->title }}</span>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-xs {{ $writeup->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">{{ $writeup->status }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">You have not written anything yet. Solve a challenge, then open it to write a writeup.</p>
            @endforelse
        </div>
    @endauth

    <h2 class="mt-6 text-lg font-semibold">Published writeups</h2>
    <div class="mt-2 space-y-2">
        @forelse ($writeups as $writeup)
            <div class="rounded border border-slate-200 bg-white p-3 text-sm">
                <a href="{{ route('writeups.show', $writeup) }}" class="font-medium text-indigo-700 underline">{{ $writeup->title }}</a>
                <div class="text-slate-500">by {{ $writeup->user->name }} on {{ $writeup->challenge->title }}</div>
            </div>
        @empty
            <p class="text-sm text-slate-500">No writeup has been published yet.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $writeups->links() }}</div>
@endsection
