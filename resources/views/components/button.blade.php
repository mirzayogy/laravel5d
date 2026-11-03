@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button', 'icon' => null])

@php
    $variants = [
        'primary' => 'bg-sky-500 text-white shadow-[0_3px_0_0_var(--color-sky-700)] hover:bg-sky-400',
        'accent' => 'bg-coral-500 text-white shadow-[0_3px_0_0_var(--color-coral-700)] hover:bg-coral-400',
        'ghost' => 'border-2 border-sky-200 bg-white text-sky-800 hover:border-sky-300 hover:bg-sky-50',
        'soft' => 'bg-sky-100 text-sky-800 hover:bg-sky-200',
        'danger' => 'border-2 border-coral-200 bg-white text-coral-700 hover:bg-coral-50',
    ];
    $sizes = [
        'sm' => 'px-3.5 py-1.5 text-sm',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    $classes = 'inline-flex items-center justify-center gap-2 rounded-full font-semibold transition duration-150 select-none active:translate-y-px disabled:pointer-events-none disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)<x-icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
    </button>
@endif
