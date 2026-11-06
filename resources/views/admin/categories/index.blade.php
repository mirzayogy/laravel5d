@extends('layouts.app')

@section('title', __('Categories'))

@section('content')
    <x-page-header :title="__('Categories')" :subtitle="__('Kelompokkan menu agar mudah dicari pelanggan.')" />

    <form method="POST" action="{{ route('admin.categories.store') }}" class="rise mb-6 flex max-w-md gap-2">
        @csrf
        <input name="name" type="text" required placeholder="{{ __('New category') }}" value="{{ old('name') }}" class="input">
        <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
    </form>

    <div class="max-w-2xl space-y-2">
        @forelse ($categories as $category)
            <div class="rise card flex flex-wrap items-center gap-3 p-3">
                <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-50">🗂️</span>
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex min-w-48 grow gap-2">
                    @csrf
                    @method('PUT')
                    <input name="name" type="text" value="{{ $category->name }}" required class="input py-1.5">
                    <button type="submit" class="btn btn-ghost btn-sm">{{ __('Rename') }}</button>
                </form>
                <span class="badge bg-stone-100 text-stone-600">{{ $category->menus_count }} {{ __('menus') }}</span>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="{{ __('Delete :name?', ['name' => $category->name]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                </form>
            </div>
        @empty
            <x-empty-state icon="🗂️" :title="__('No category yet')" :text="__('Add your first category using the form above.')" />
        @endforelse
    </div>
@endsection
