@props(['title', 'subtitle' => null, 'icon' => null, 'crumbs' => []])

<div {{ $attributes->class(['mb-6']) }}>
    @if (count($crumbs))
        <nav class="mb-2 flex flex-wrap items-center gap-1 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            @foreach ($crumbs as $label => $url)
                @if ($url)
                    <a href="{{ $url }}" class="hover:text-sky-700">{{ $label }}</a>
                    <span aria-hidden="true">/</span>
                @else
                    <span class="text-slate-700">{{ $label }}</span>
                @endif
            @endforeach
        </nav>
    @endif
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            @if ($icon)
                <span class="mt-0.5 flex size-11 shrink-0 items-center justify-center rounded-2xl bg-sky-500 text-white shadow-[0_3px_0_0_var(--color-sky-700)]">
                    <x-icon :name="$icon" class="size-6" />
                </span>
            @endif
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-1 max-w-2xl text-sm text-slate-600">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if (isset($actions))
            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
        @endif
    </div>
</div>
