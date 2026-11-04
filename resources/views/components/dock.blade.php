@props(['items'])

<nav class="dock lg:hidden" data-dock aria-label="{{ __('Quick navigation') }}">
    @foreach ($items as $item)
        @if (! empty($item['toggle']))
            <button type="button" class="dock-item" data-sidebar-toggle aria-label="{{ $item['label'] }}">
                <span class="dock-tile"><x-icon :name="$item['icon']" class="size-5" /></span>
                <span class="dock-label">{{ $item['label'] }}</span>
            </button>
        @else
            <a href="{{ $item['url'] }}" class="dock-item" @if ($item['active']) aria-current="page" @endif aria-label="{{ $item['label'] }}">
                <span class="dock-tile"><x-icon :name="$item['icon']" class="size-5" /></span>
                <span class="dock-dot"></span>
                <span class="dock-label">{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
</nav>
