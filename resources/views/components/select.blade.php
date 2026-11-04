@props(['name', 'options', 'value' => null, 'placeholder' => __('Select…'), 'label' => null, 'compact' => false, 'autosubmit' => false])

<div {{ $attributes->class(['gs', 'gs-compact' => $compact]) }} data-glide-select
    data-name="{{ $name }}" data-value="{{ $value }}" data-placeholder="{{ $placeholder }}"
    data-label="{{ $label ?? $placeholder }}" data-options="{{ json_encode(array_values($options)) }}"
    @if ($autosubmit) data-autosubmit @endif></div>
