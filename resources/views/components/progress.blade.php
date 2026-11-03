@props(['value' => 0, 'tone' => 'sky'])

@php
    $tones = ['sky' => 'bg-sky-500', 'coral' => 'bg-coral-500', 'green' => 'bg-emerald-500', 'amber' => 'bg-amber-400'];
    $value = max(0, min(100, (int) round($value)));
@endphp

<div {{ $attributes->class(['h-2.5 overflow-hidden rounded-full bg-sky-100']) }} role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100">
    <div class="h-full rounded-full transition-all {{ $tones[$tone] ?? $tones['sky'] }}" style="width: {{ $value }}%"></div>
</div>
