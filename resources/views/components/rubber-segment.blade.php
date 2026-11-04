@props(['items', 'value', 'label' => __('Segmented control'), 'size' => 'md'])

{{-- items: [['value' => 'list', 'label' => 'List', 'icon' => 'list', 'href' => url, 'count' => 3]] --}}
<div {{ $attributes }} data-rubber-segment data-value="{{ $value }}" data-size="{{ $size }}" aria-label="{{ $label }}" class="rs">
    @foreach ($items as $item)
        <button type="button" class="rs-item" data-value="{{ $item['value'] }}" @isset($item['href']) data-href="{{ $item['href'] }}" @endisset>
            @isset($item['icon'])<x-icon :name="$item['icon']" class="size-3.5" />@endisset
            {{ $item['label'] }}
            @isset($item['count'])<span class="rs-count" data-count="{{ $item['value'] }}">{{ $item['count'] }}</span>@endisset
        </button>
    @endforeach
</div>
