@props(['name'])

@php
    $map = [
        'Web' => ['globe', 'sky'],
        'Cryptography' => ['lock', 'violet'],
        'Forensics' => ['search', 'amber'],
        'Reverse Engineering' => ['cpu', 'green'],
        'Pwn' => ['terminal', 'coral'],
        'Miscellaneous' => ['star', 'slate'],
    ];
    [$icon, $tone] = $map[$name] ?? ['tag', 'slate'];
@endphp

<x-badge :tone="$tone" {{ $attributes }}>
    <x-icon :name="$icon" class="size-3.5" />
    {{ $name }}
</x-badge>
