@extends('layouts.app')

@section('title', $menu ? __('Edit menu') : __('New menu'))

@section('content')
    <x-page-header :title="$menu ? __('Edit menu') : __('New menu')" :subtitle="$menu ? $menu->name : __('Fill in the details shown on the customer menu page.')" />

    @if ($categories->isEmpty())
        <x-empty-state icon="🗂️" :title="__('Create a category first')" :text="__('A menu must belong to a category.')">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">{{ __('Go to categories') }}</a>
        </x-empty-state>
    @else
        <form method="POST" action="{{ $menu ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" enctype="multipart/form-data" class="rise card max-w-2xl space-y-5 p-6">
            @csrf
            @if ($menu)
                @method('PUT')
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="category_id" class="label">{{ __('Category') }}</label>
                    <select id="category_id" name="category_id" required class="input">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $menu?->category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="price" class="label">{{ __('Price (Rp)') }}</label>
                    <input id="price" name="price" type="number" min="0" max="1000000" step="500" value="{{ old('price', $menu?->price ?? 0) }}" required class="input">
                </div>
                <div>
                    <label for="stock" class="label">{{ __('Stock') }}</label>
                    <input id="stock" name="stock" type="number" min="0" max="9999" value="{{ old('stock', $menu?->stock) }}" placeholder="{{ __('Leave empty for unlimited') }}" class="input">
                </div>
            </div>

            <div>
                <span class="label">{{ __('Photo') }}</span>
                <div class="flex flex-wrap items-center gap-4">
                    @if ($menu?->image_url)
                        <img id="photo-preview" src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="h-24 w-24 rounded-xl object-cover">
                    @else
                        <img id="photo-preview" src="" alt="" class="hidden h-24 w-24 rounded-xl object-cover">
                        <div data-image-placeholder class="grid h-24 w-24 place-items-center rounded-xl border border-dashed border-stone-300 bg-stone-50 text-2xl text-stone-400">📷</div>
                    @endif
                    <div class="space-y-2">
                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" data-image-input="#photo-preview" class="block text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                        <p class="text-xs text-stone-500">{{ __('JPG, PNG, or WebP, max 2 MB.') }}</p>
                        @if ($menu?->image)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="remove_image" value="1" class="h-4 w-4 rounded border-stone-300 accent-brand-600"> {{ __('Remove current photo') }}
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <label for="name" class="label">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $menu?->name) }}" required class="input" placeholder="{{ __('Contoh: Kopi Susu Gula Aren') }}">
            </div>
            <div>
                <label for="description" class="label">{{ __('Description') }}</label>
                <textarea id="description" name="description" rows="3" class="input" placeholder="{{ __('Ceritakan rasa dan bahan menu ini') }}">{{ old('description', $menu?->description) }}</textarea>
            </div>

            <div>
                <span class="label">{{ __('Tags') }}</span>
                @php($selectedTags = old('tag_ids', $menu?->tags->pluck('id')->all() ?? []))
                <div class="flex flex-wrap gap-2">
                    @forelse ($tags as $tag)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" class="peer sr-only" @checked(in_array($tag->id, $selectedTags))>
                            <span class="chip peer-checked:border-brand-600 peer-checked:bg-brand-600 peer-checked:text-white peer-focus-visible:outline-2">{{ $tag->name }}</span>
                        </label>
                    @empty
                        <span class="text-sm text-stone-500">{{ __('No tag yet.') }} <a href="{{ route('admin.tags.index') }}" class="text-brand-700 underline">{{ __('Add tags') }}</a>.</span>
                    @endforelse
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded border-stone-300 accent-brand-600" @checked(old('is_available', $menu?->is_available ?? true))>
                {{ __('Available for ordering') }}
            </label>

            <div class="flex gap-2 border-t border-stone-100 pt-5">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a href="{{ route('admin.menus.index') }}" class="btn btn-ghost">{{ __('Back') }}</a>
            </div>
        </form>
    @endif
@endsection
