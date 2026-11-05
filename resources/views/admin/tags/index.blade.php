@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <x-page-header title="Tags" subtitle="Label seperti Pedas, Vegetarian, atau Best Seller." />

    <form method="POST" action="{{ route('admin.tags.store') }}" class="rise mb-6 flex max-w-md gap-2">
        @csrf
        <input name="name" type="text" required placeholder="New tag" value="{{ old('name') }}" class="input">
        <button type="submit" class="btn btn-primary">Add</button>
    </form>

    <div class="max-w-2xl space-y-2">
        @forelse ($tags as $tag)
            <div class="rise card flex flex-wrap items-center gap-3 p-3">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-50">🏷️</span>
                <form method="POST" action="{{ route('admin.tags.update', $tag) }}" class="flex min-w-48 grow gap-2">
                    @csrf
                    @method('PUT')
                    <input name="name" type="text" value="{{ $tag->name }}" required class="input py-1.5">
                    <button type="submit" class="btn btn-ghost btn-sm">Rename</button>
                </form>
                <span class="badge bg-stone-100 text-stone-600">{{ $tag->menus_count }} menus</span>
                <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" data-confirm="Delete {{ $tag->name }}?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        @empty
            <x-empty-state icon="🏷️" title="No tag yet" text="Add your first tag using the form above." />
        @endforelse
    </div>
@endsection
