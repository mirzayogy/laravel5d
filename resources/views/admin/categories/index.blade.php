@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <h1 class="text-2xl font-semibold">Categories</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 flex max-w-md gap-2">
        @csrf
        <input name="name" type="text" required placeholder="New category" value="{{ old('name') }}"
               class="grow rounded border border-stone-300 px-3 py-2 text-sm">
        <button type="submit" class="rounded bg-amber-600 px-3 py-2 text-sm text-white hover:bg-amber-500">Add</button>
    </form>

    <div class="mt-4 max-w-xl space-y-2">
        @forelse ($categories as $category)
            <div class="flex items-center gap-2 rounded border border-stone-200 bg-white p-2 text-sm">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex grow gap-2">
                    @csrf
                    @method('PUT')
                    <input name="name" type="text" value="{{ $category->name }}" required
                           class="grow rounded border border-stone-300 px-2 py-1">
                    <button type="submit" class="rounded border border-stone-300 px-2 py-1 hover:bg-stone-100">Rename</button>
                </form>
                <span class="text-xs text-stone-500">{{ $category->menus_count }} menus</span>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded border border-red-300 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-stone-500">No category yet.</p>
        @endforelse
    </div>
@endsection
