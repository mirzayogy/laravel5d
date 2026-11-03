@props(['target', 'doneLabel' => 'Time is up'])

@php
    $left = max(0, (int) now()->diffInSeconds($target, false));
    $parts = [
        'days' => ['d', intdiv($left, 86400)],
        'hours' => ['h', intdiv($left, 3600) % 24],
        'minutes' => ['m', intdiv($left, 60) % 60],
        'seconds' => ['s', $left % 60],
    ];
@endphp

<div x-data="countdown('{{ $target->toIso8601String() }}')" {{ $attributes->class(['inline-flex items-center gap-1.5']) }}>
    <template x-if="done">
        <span class="text-sm font-semibold text-slate-600">{{ $doneLabel }}</span>
    </template>
    <div x-show="! done" class="flex items-center gap-1.5" @if ($left === 0) style="display:none" @endif>
        @foreach ($parts as $unit => [$short, $initial])
            <span class="flex min-w-12 flex-col items-center rounded-2xl bg-white px-2 py-1 shadow-[0_3px_0_0_var(--color-sky-100)] ring-2 ring-sky-100">
                <span class="text-lg leading-none font-extrabold text-slate-800 tabular-nums" x-text="pad({{ $unit }})">{{ str_pad((string) $initial, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="text-[10px] font-semibold text-slate-500 uppercase">{{ $short }}</span>
            </span>
        @endforeach
    </div>
    @if ($left === 0)
        <span class="text-sm font-semibold text-slate-600" x-show="false">{{ $doneLabel }}</span>
    @endif
</div>
