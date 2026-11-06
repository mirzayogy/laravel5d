@props(['title', 'subtitle' => null])

<div class="rise mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-stone-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-1 text-sm text-stone-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if (! $slot->isEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
    @endif
</div>
