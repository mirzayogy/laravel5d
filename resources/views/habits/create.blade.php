<x-app-layout title="{{ __('Add habit') }}" :crumbs="[[__('Habits'), route('habits.index')], [__('Add')]]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">{{ __('Add habit') }}</h1>
            <a href="{{ route('habits.index') }}" class="btn btn-outline btn-sm">
                <x-icon name="arrow-left" class="size-3.5" /> {{ __('Back') }}
            </a>
        </div>
        <form action="{{ route('habits.store') }}" method="POST" class="card-body">
            @csrf
            
            @include('habits._form')

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('habits.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> {{ __('Save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
