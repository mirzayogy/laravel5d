@props(['padded' => true, 'hover' => false, 'tone' => 'white'])

@php
    $tones = [
        'white' => 'border-sky-100 bg-white shadow-[0_5px_0_0_var(--color-sky-100)]',
        'sky' => 'border-sky-200 bg-sky-50 shadow-[0_5px_0_0_var(--color-sky-200)]',
        'coral' => 'border-coral-200 bg-coral-50 shadow-[0_5px_0_0_var(--color-coral-200)]',
        'green' => 'border-emerald-200 bg-emerald-50 shadow-[0_5px_0_0_var(--color-emerald-200)]',
    ];
@endphp

<div {{ $attributes->class([
    'rounded-3xl border-2',
    $tones[$tone] ?? $tones['white'],
    'p-5' => $padded,
    'transition duration-150 hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-[0_7px_0_0_var(--color-sky-200)]' => $hover,
]) }}>
    {{ $slot }}
</div>
