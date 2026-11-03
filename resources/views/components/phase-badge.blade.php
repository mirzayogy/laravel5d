@props(['phase'])

@php
    $map = [
        'running' => ['green', 'Live'],
        'upcoming' => ['sky', 'Upcoming'],
        'finished' => ['slate', 'Finished'],
        'draft' => ['amber', 'Draft'],
    ];
    [$tone, $label] = $map[$phase] ?? ['slate', ucfirst($phase)];
@endphp

<x-badge :tone="$tone" {{ $attributes }}>
    @if ($phase === 'running')
        <span class="relative flex size-2">
            <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
        </span>
    @endif
    {{ $label }}
</x-badge>
