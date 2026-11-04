<x-app-layout title="{{ __('Edit category') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Categories'), route('admin.categories.index')], [__('Edit')]]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">{{ __('Edit category') }}</h1>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm">
                <x-icon name="arrow-left" class="size-3.5" /> {{ __('Back') }}
            </a>
        </div>
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            @include('categories._form', ['category' => $category])

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> {{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
