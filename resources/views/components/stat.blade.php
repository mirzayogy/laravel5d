@props(['icon', 'label', 'value', 'tone' => 'sky', 'hint' => null])

@php
    $tones = [
        'sky' => 'bg-sky-100 text-sky-700',
        'coral' => 'bg-coral-100 text-coral-700',
        'green' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'violet' => 'bg-violet-100 text-violet-700',
    ];
@endphp

<x-card {{ $attributes->class(['flex items-center gap-4']) }}>
    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl {{ $tones[$tone] ?? $tones['sky'] }}">
        <x-icon :name="$icon" class="size-6" />
    </span>
    <div class="min-w-0">
        <div class="text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $label }}</div>
        <div class="truncate text-2xl leading-tight font-extrabold tracking-tight text-slate-800 tabular-nums">{{ $value }}</div>
        @if ($hint)
            <div class="truncate text-xs text-slate-500">{{ $hint }}</div>
        @endif
    </div>
</x-card>
