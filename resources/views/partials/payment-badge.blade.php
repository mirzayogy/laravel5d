@php
    [$badge, $dot] = match ($status) {
        'paid' => ['bg-emerald-50 text-emerald-800', 'bg-emerald-500'],
        'refunded' => ['bg-stone-100 text-stone-600', 'bg-stone-400'],
        default => ['bg-rose-50 text-rose-700', 'bg-rose-500'],
    };
@endphp
<span class="badge {{ $badge }}"><span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>{{ __($status) }}</span>
