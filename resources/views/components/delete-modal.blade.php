@props(['id', 'action', 'title', 'message'])

<dialog id="{{ $id }}" aria-labelledby="{{ $id }}-title"
    class="m-auto w-full max-w-md rounded-xl border border-mist/60 bg-paper p-0 text-left text-ink backdrop:bg-ink/40">
    <div class="p-6">
        <h2 id="{{ $id }}-title" class="text-subheading">{{ $title }}</h2>
        <p class="mt-2 text-body-sm text-graphite">{{ $message }} {{ __('This cannot be undone.') }}</p>
    </div>
    <div class="flex justify-end gap-2 border-t border-mist/60 px-6 py-4">
        <button type="button" class="btn btn-outline" data-modal-close>{{ __('Cancel') }}</button>
        <form action="{{ $action }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <x-icon name="trash" /> {{ __('Delete') }}
            </button>
        </form>
    </div>
</dialog>
