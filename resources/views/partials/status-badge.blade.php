@php
    [$badge, $dot] = match ($status) {
        'pending' => ['bg-amber-50 text-amber-800', 'bg-amber-500'],
        'preparing' => ['bg-sky-50 text-sky-800', 'bg-sky-500'],
        'completed' => ['bg-emerald-50 text-emerald-800', 'bg-emerald-500'],
        default => ['bg-stone-100 text-stone-600', 'bg-stone-400'],
    };
@endphp
<span class="badge {{ $badge }}"><span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ $status }}</span>
