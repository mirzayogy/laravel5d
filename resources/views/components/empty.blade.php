@props(['icon' => 'info', 'title', 'text' => null])

<div {{ $attributes->class(['rounded-3xl border-2 border-dashed border-sky-200 bg-sky-50/60 px-6 py-10 text-center']) }}>
    <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-white text-sky-500 shadow-[0_4px_0_0_var(--color-sky-100)]">
        <x-icon :name="$icon" class="size-7" />
    </span>
    <h3 class="mt-4 text-base font-bold text-slate-800">{{ $title }}</h3>
    @if ($text)
        <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">{{ $text }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-4 flex flex-wrap justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
