@props(['name', 'size' => 'md'])

@php
    $colors = [
        'bg-sky-200 text-sky-800',
        'bg-coral-200 text-coral-800',
        'bg-emerald-200 text-emerald-800',
        'bg-violet-200 text-violet-800',
        'bg-amber-200 text-amber-800',
        'bg-pink-200 text-pink-800',
    ];
    $sizes = ['sm' => 'size-8 text-xs', 'md' => 'size-10 text-sm', 'lg' => 'size-14 text-lg'];
    $words = preg_split('/\s+/', trim($name)) ?: [];
    $initials = mb_strtoupper(mb_substr($words[0] ?? '?', 0, 1).mb_substr(count($words) > 1 ? end($words) : '', 0, 1));
@endphp

<span {{ $attributes->class(['inline-flex shrink-0 items-center justify-center rounded-full font-bold', $colors[crc32($name) % count($colors)], $sizes[$size] ?? $sizes['md']]) }} title="{{ $name }}">{{ $initials }}</span>
