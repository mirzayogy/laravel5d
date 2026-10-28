@extends('layouts.app')

@section('title', $writeup ? 'Edit writeup' : 'New writeup')

@section('content')
    <h1 class="text-2xl font-semibold">{{ $writeup ? 'Edit writeup' : 'New writeup' }}</h1>
    <p class="mt-1 text-sm text-slate-500">Challenge: {{ $challenge->title }}</p>

    <form method="POST"
          action="{{ $writeup ? route('writeups.update', $writeup) : route('writeups.store', $challenge) }}"
          class="mt-4 space-y-4">
        @csrf
        @if ($writeup)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="block text-sm font-medium">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $writeup?->title) }}" required
                   class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="content" class="block text-sm font-medium">Content</label>
            <textarea id="content" name="content" rows="12" required
                      class="mt-1 w-full rounded border border-slate-300 px-3 py-2">{{ old('content', $writeup?->content) }}</textarea>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium">Status</label>
            <select id="status" name="status" class="mt-1 rounded border border-slate-300 px-3 py-2">
                @foreach (['draft', 'published'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $writeup?->status ?? 'draft') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Save</button>
    </form>
@endsection
