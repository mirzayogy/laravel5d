@extends('layouts.app')

@section('title', $menu ? 'Edit menu' : 'New menu')

@section('content')
    <x-page-header :title="$menu ? 'Edit menu' : 'New menu'" :subtitle="$menu ? $menu->name : 'Isi detail menu yang akan tampil di halaman pelanggan.'" />

    @if ($categories->isEmpty())
        <x-empty-state icon="🗂️" title="Create a category first" text="A menu must belong to a category.">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">Go to categories</a>
        </x-empty-state>
    @else
        <form method="POST" action="{{ $menu ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" class="rise card max-w-2xl space-y-5 p-6">
            @csrf
            @if ($menu)
                @method('PUT')
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="category_id" class="label">Category</label>
                    <select id="category_id" name="category_id" required class="input">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $menu?->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price" class="label">Price (Rp)</label>
                    <input id="price" name="price" type="number" min="0" max="1000000" step="500" value="{{ old('price', $menu?->price ?? 0) }}" required class="input">
                </div>
            </div>

            <div>
                <label for="name" class="label">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $menu?->name) }}" required class="input" placeholder="Contoh: Kopi Susu Gula Aren">
            </div>
            <div>
                <label for="description" class="label">Description</label>
                <textarea id="description" name="description" rows="3" class="input" placeholder="Ceritakan rasa dan bahan menu ini">{{ old('description', $menu?->description) }}</textarea>
            </div>

            <div>
                <span class="label">Tags</span>
                @php($selectedTags = old('tag_ids', $menu?->tags->pluck('id')->all() ?? []))
                <div class="flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" class="peer sr-only" @checked(in_array($tag->id, $selectedTags))>
                            <span class="chip peer-checked:border-brand-600 peer-checked:bg-brand-600 peer-checked:text-white peer-focus-visible:outline-2">{{ $tag->name }}</span>
                        </label>
                    @empty
                        <span class="text-sm text-stone-500">No tag yet. <a href="{{ route('admin.tags.index') }}" class="text-brand-700 underline">Add tags</a>.</span>
                    @endforelse
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded border-stone-300 accent-brand-600" @checked(old('is_available', $menu?->is_available ?? true))>
                Available for ordering
            </label>

            <div class="flex gap-2 border-t border-stone-100 pt-5">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-ghost">Back</a>
            </div>
        </form>
    @endif
@endsection
