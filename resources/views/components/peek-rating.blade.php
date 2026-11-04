@props(['name' => null, 'value' => 0, 'count' => 5, 'labels' => [], 'shape' => 'star', 'clear' => false, 'label' => __('Rating')])

<div data-peek-rating data-count="{{ $count }}" data-value="{{ $value }}" data-shape="{{ $shape }}"
    data-clear="{{ $clear ? 'true' : 'false' }}" data-label="{{ $label }}"
    data-labels="{{ json_encode(array_values($labels)) }}" @if ($name) data-name="{{ $name }}" @endif
    {{ $attributes }}></div>
