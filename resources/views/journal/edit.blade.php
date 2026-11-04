<x-app-layout title="{{ __('Edit journal entry') }}" :crumbs="[[__('Track')], [__('Journal'), route('journal.index')], [__('Edit')]]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">{{ __('Edit journal entry') }}</h1>
            <a href="{{ route('journal.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> {{ __('Back') }}</a>
        </div>
        <form action="{{ route('journal.update', $journal) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            @include('journal._form', ['journal' => $journal])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('journal.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> {{ __('Save changes') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
