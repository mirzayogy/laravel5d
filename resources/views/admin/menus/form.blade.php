@extends('layouts.app')

@section('title', $menu ? 'Edit menu' : 'New menu')

@section('content')
    <h1 class="text-2xl font-semibold">{{ $menu ? 'Edit menu' : 'New menu' }}</h1>

    <form method="POST"
          action="{{ $menu ? route('admin.menus.update', $menu) : route('admin.menus.store') }}"
          class="mt-4 max-w-xl space-y-4">
        @csrf
        @if ($menu)
            @method('PUT')
        @endif

        <div>
            <label for="category_id" class="block text-sm font-medium">Category</label>
            <select id="category_id" name="category_id" required class="mt-1 w-full rounded border border-stone-300 px-3 py-2">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $menu?->category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="name" class="block text-sm font-medium">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $menu?->name) }}" required
                   class="mt-1 w-full rounded border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label for="description" class="block text-sm font-medium">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="mt-1 w-full rounded border border-stone-300 px-3 py-2">{{ old('description', $menu?->description) }}</textarea>
        </div>
        <div>
            <label for="price" class="block text-sm font-medium">Price (Rp)</label>
            <input id="price" name="price" type="number" min="0" value="{{ old('price', $menu?->price ?? 0) }}" required
                   class="mt-1 w-40 rounded border border-stone-300 px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $menu?->is_available ?? true))> Available
        </label>

        <div>
            <span class="block text-sm font-medium">Tags</span>
            <div class="mt-1 flex flex-wrap gap-3 text-sm">
                @php($selectedTags = old('tag_ids', $menu?->tags->pluck('id')->all() ?? []))
                @forelse ($tags as $tag)
                    <label class="flex items-center gap-1">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))> {{ $tag->name }}
                    </label>
                @empty
                    <span class="text-stone-500">No tag yet. Add tags on the Tags page.</span>
                @endforelse
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="rounded bg-amber-600 px-3 py-2 text-sm text-white hover:bg-amber-500">Save</button>
            <a href="{{ route('admin.menus.index') }}" class="rounded border border-stone-300 px-3 py-2 text-sm hover:bg-stone-100">Back</a>
        </div>
    </form>
@endsection
