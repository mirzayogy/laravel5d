@extends('layouts.app')

@section('title', $writeup->title)

@section('content')
    <a href="{{ route('writeups.index') }}" class="text-sm text-indigo-700 underline">Back to writeups</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $writeup->title }}</h1>
        <span class="rounded-full px-2 py-0.5 text-xs {{ $writeup->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-slate-200 text-slate-700' }}">{{ $writeup->status }}</span>
    </div>
    <p class="mt-1 text-sm text-slate-500">by {{ $writeup->user->name }} on {{ $writeup->challenge->title }}</p>

    <div class="mt-4 whitespace-pre-line rounded border border-slate-200 bg-white p-4">{{ $writeup->content }}</div>

    @if ($isAuthor)
        <div class="mt-4 flex gap-3">
            <a href="{{ route('writeups.edit', $writeup) }}" class="rounded border border-slate-300 px-3 py-1 text-sm hover:bg-slate-100">Edit</a>
            <form method="POST" action="{{ route('writeups.destroy', $writeup) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded border border-red-300 px-3 py-1 text-sm text-red-700 hover:bg-red-50">Delete</button>
            </form>
        </div>
    @endif
@endsection
