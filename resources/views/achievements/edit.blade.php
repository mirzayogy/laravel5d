<x-app-layout title="{{ __('Edit achievement') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Achievements'), route('admin.achievements.index')], [__('Edit')]]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">{{ __('Edit achievement') }}</h1>
            <a href="{{ route('admin.achievements.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> {{ __('Back') }}</a>
        </div>
        <form action="{{ route('admin.achievements.update', $achievement) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            @include('achievements._form', ['achievement' => $achievement, 'type' => $type, 'threshold' => $threshold])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.achievements.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> {{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
