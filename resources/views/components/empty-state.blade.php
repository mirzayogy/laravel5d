@props(['icon' => '☕', 'title', 'text' => null])

<div {{ $attributes->merge(['class' => 'rise flex flex-col items-center rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-12 text-center']) }}>
    <div class="grid h-14 w-14 place-items-center rounded-2xl bg-brand-50 text-2xl">{{ $icon }}</div>
    <h3 class="mt-4 text-base font-semibold text-stone-800">{{ $title }}</h3>
    @if ($text)
        <p class="mt-1 max-w-sm text-sm text-stone-500">{{ $text }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-5 flex flex-wrap justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
