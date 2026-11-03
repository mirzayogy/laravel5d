@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <x-page-header title="Categories" subtitle="The kinds of challenges players can solve." icon="layers" />

    @include('partials.admin-nav')

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="self-start">
            <h2 class="font-extrabold">New category</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-3 space-y-3">
                @csrf
                <x-input name="name" label="Name" required />
                <x-button type="submit" icon="plus">Add category</x-button>
            </form>
        </x-card>

        <div class="space-y-3 lg:col-span-2">
            @forelse ($categories as $category)
                <x-card :padded="false" class="flex flex-wrap items-center gap-3 p-3">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex min-w-48 grow items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input name="name" type="text" value="{{ $category->name }}" required aria-label="Name" class="w-full rounded-full border-2 border-sky-100 bg-white px-4 py-2 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                        <x-button type="submit" size="sm" variant="soft" icon="check">Rename</x-button>
                    </form>
                    <x-badge tone="sky">{{ $category->challenges_count }} challenges</x-badge>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" size="sm" variant="danger" icon="trash">Delete</x-button>
                    </form>
                </x-card>
            @empty
                <x-empty icon="layers" title="Nothing here yet" text="Add the first category on the left." />
            @endforelse
        </div>
    </div>
@endsection
