@extends('layouts.app')

@section('title', $competition ? 'Edit competition' : 'New competition')

@section('content')
    <h1 class="text-2xl font-semibold">{{ $competition ? 'Edit competition' : 'New competition' }}</h1>

    <form method="POST"
          action="{{ $competition ? route('admin.competitions.update', $competition) : route('admin.competitions.store') }}"
          class="mt-4 max-w-xl space-y-4">
        @csrf
        @if ($competition)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="block text-sm font-medium">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $competition?->title) }}" required
                   class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="slug" class="block text-sm font-medium">Slug (optional)</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $competition?->slug) }}"
                   class="mt-1 w-full rounded border border-slate-300 px-3 py-2 font-mono">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium">Description</label>
            <textarea id="description" name="description" rows="4"
                      class="mt-1 w-full rounded border border-slate-300 px-3 py-2">{{ old('description', $competition?->description) }}</textarea>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="start_at" class="block text-sm font-medium">Start</label>
                <input id="start_at" name="start_at" type="datetime-local" required
                       value="{{ old('start_at', $competition?->start_at?->format('Y-m-d\TH:i')) }}"
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="end_at" class="block text-sm font-medium">End</label>
                <input id="end_at" name="end_at" type="datetime-local" required
                       value="{{ old('end_at', $competition?->end_at?->format('Y-m-d\TH:i')) }}"
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium">Status</label>
            <select id="status" name="status" class="mt-1 rounded border border-slate-300 px-3 py-2">
                @foreach (['draft', 'running', 'finished'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $competition?->status ?? 'draft') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Save</button>
            <a href="{{ route('admin.competitions.index') }}" class="rounded border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100">Cancel</a>
        </div>
    </form>
@endsection
