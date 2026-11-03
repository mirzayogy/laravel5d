@props(['level'])

@php
    $map = ['easy' => 'green', 'medium' => 'amber', 'hard' => 'coral'];
@endphp

<x-badge :tone="$map[$level] ?? 'slate'" {{ $attributes }}>{{ ucfirst($level) }}</x-badge>
