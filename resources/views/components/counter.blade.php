@props(['value'])

<span data-counter="{{ $value }}" {{ $attributes->class('ctr') }}>{{ $value }}</span>
