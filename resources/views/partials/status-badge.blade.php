@php
    $badge = match ($status) {
        'pending' => 'bg-amber-100 text-amber-800',
        'preparing' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        default => 'bg-stone-200 text-stone-700',
    };
@endphp
<span class="rounded-full px-2 py-0.5 text-xs {{ $badge }}">{{ $status }}</span>
