@props(['tone' => 'sky'])

@php
    $tones = [
        'sky' => 'bg-sky-100 text-sky-800 ring-sky-200',
        'coral' => 'bg-coral-100 text-coral-800 ring-coral-200',
        'green' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
        'amber' => 'bg-amber-100 text-amber-800 ring-amber-200',
        'violet' => 'bg-violet-100 text-violet-800 ring-violet-200',
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset', $tones[$tone] ?? $tones['sky']]) }}>{{ $slot }}</span>
