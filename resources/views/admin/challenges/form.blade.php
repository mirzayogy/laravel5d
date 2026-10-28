@extends('layouts.app')

@section('title', $challenge ? 'Edit challenge' : 'New challenge')

@section('content')
    <h1 class="text-2xl font-semibold">{{ $challenge ? 'Edit challenge' : 'New challenge' }}</h1>

    <form method="POST"
          action="{{ $challenge ? route('admin.challenges.update', $challenge) : route('admin.challenges.store') }}"
          class="mt-4 max-w-xl space-y-4">
        @csrf
        @if ($challenge)
            @method('PUT')
        @endif

        <div>
            <label for="competition_id" class="block text-sm font-medium">Competition</label>
            <select id="competition_id" name="competition_id" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
                @foreach ($competitions as $competition)
                    <option value="{{ $competition->id }}" @selected((int) old('competition_id', $challenge?->competition_id) === $competition->id)>{{ $competition->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="category_id" class="block text-sm font-medium">Category</label>
            <select id="category_id" name="category_id" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $challenge?->category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="title" class="block text-sm font-medium">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $challenge?->title) }}" required
                   class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium">Description</label>
            <textarea id="description" name="description" rows="5" required
                      class="mt-1 w-full rounded border border-slate-300 px-3 py-2">{{ old('description', $challenge?->description) }}</textarea>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="points" class="block text-sm font-medium">Points</label>
                <input id="points" name="points" type="number" min="1" value="{{ old('points', $challenge?->points ?? 100) }}" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label for="difficulty" class="block text-sm font-medium">Difficulty</label>
                <select id="difficulty" name="difficulty" class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
                    @foreach (['easy', 'medium', 'hard'] as $level)
                        <option value="{{ $level }}" @selected(old('difficulty', $challenge?->difficulty ?? 'easy') === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label for="flag" class="block text-sm font-medium">Flag {{ $challenge ? '(leave empty to keep the current flag)' : '' }}</label>
            <input id="flag" name="flag" type="text" autocomplete="off" {{ $challenge ? '' : 'required' }}
                   class="mt-1 w-full rounded border border-slate-300 px-3 py-2 font-mono">
            <p class="mt-1 text-xs text-slate-500">The flag is stored as a hash and cannot be shown again.</p>
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $challenge?->is_published))> Published
        </label>

        <div>
            <span class="block text-sm font-medium">Tags</span>
            <div class="mt-1 flex flex-wrap gap-3 text-sm">
                @php($selectedTags = old('tag_ids', $challenge?->tags->pluck('id')->all() ?? []))
                @forelse ($tags as $tag)
                    <label class="flex items-center gap-1">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))> {{ $tag->name }}
                    </label>
                @empty
                    <span class="text-slate-500">No tag yet.</span>
                @endforelse
            </div>
            <input name="new_tags" type="text" value="{{ old('new_tags') }}" placeholder="New tags, separated by commas"
                   class="mt-2 w-full rounded border border-slate-300 px-3 py-2 text-sm">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Save</button>
            <a href="{{ route('admin.challenges.index') }}" class="rounded border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100">Back</a>
        </div>
    </form>

    @if ($challenge)
        <h2 class="mt-10 text-lg font-semibold">Hints</h2>
        <div class="mt-2 max-w-xl space-y-2">
            @forelse ($challenge->hints as $hint)
                <div class="flex items-start justify-between gap-3 rounded border border-slate-200 bg-white p-3 text-sm">
                    <div>
                        <p class="whitespace-pre-line">{{ $hint->content }}</p>
                        <p class="mt-1 text-xs text-slate-500">Costs {{ $hint->cost }} points</p>
                    </div>
                    <form method="POST" action="{{ route('admin.hints.destroy', $hint) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded border border-red-300 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-500">No hint yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('admin.hints.store', $challenge) }}" class="mt-4 max-w-xl space-y-3">
            @csrf
            <div>
                <label for="hint_content" class="block text-sm font-medium">New hint</label>
                <textarea id="hint_content" name="content" rows="2" required
                          class="mt-1 w-full rounded border border-slate-300 px-3 py-2"></textarea>
            </div>
            <div>
                <label for="hint_cost" class="block text-sm font-medium">Cost (points)</label>
                <input id="hint_cost" name="cost" type="number" min="0" value="10" required
                       class="mt-1 w-32 rounded border border-slate-300 px-3 py-2">
            </div>
            <button type="submit" class="rounded border border-slate-300 px-3 py-2 text-sm hover:bg-slate-100">Add hint</button>
        </form>
    @endif
@endsection
