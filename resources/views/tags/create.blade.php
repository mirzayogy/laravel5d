<x-app-layout title="{{ __('Add tag') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Tags'), route('admin.tags.index')], [__('Add')]]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">{{ __('Add tag') }}</h1>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-outline btn-sm">
                <x-icon name="arrow-left" class="size-3.5" /> {{ __('Back') }}
            </a>
        </div>
        <form action="{{ route('admin.tags.store') }}" method="POST" class="card-body">
            @csrf
            
            @include('tags._form')

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.tags.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> {{ __('Save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
