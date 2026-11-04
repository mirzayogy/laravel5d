{{-- Flash messages are shown as toasts (see ui/toast.js); the noscript copy keeps them visible without JavaScript. --}}
@foreach (['success', 'error'] as $kind)
    @if (session($kind))
        <span data-flash="{{ $kind }}" data-message="{{ session($kind) }}"
            @if (session('undo')) data-undo="{{ session('undo') }}" @endif
            @if (session('action_url')) data-link="{{ session('action_url') }}" data-link-label="{{ session('action_label', 'Open') }}" @endif hidden></span>
        <noscript>
            <div role="{{ $kind === 'error' ? 'alert' : 'status' }}" class="mb-4 flex items-center gap-2 rounded-xl border border-mist/60 bg-fog px-4 py-3 text-body-sm text-ink">
                <x-icon name="{{ $kind === 'error' ? 'circle-alert' : 'circle-check' }}" class="size-4 shrink-0" />
                {{ session($kind) }}
            </div>
        </noscript>
    @endif
@endforeach
