@props(['text' => true, 'badge' => null, 'large' => false])

@php
    // null = show the release stage (Beta); pass a string to override, or false to hide.
    $label = $badge === null ? (config('habitude.stage') === 'production' ? null : ucfirst(config('habitude.stage'))) : ($badge ?: null);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <svg class="{{ $large ? 'h-12' : 'h-8' }} w-auto shrink-0" viewBox="0 0 40 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M18.5 19V4L21.5 4V19H18.5Z"/>
        <path d="M18.5 44V29H21.5V44H18.5Z"/>
        <path d="M22.4754 19.404L33.082 8.79736L35.2034 10.9187L26.7181 19.404L22.4754 19.404Z"/>
        <path d="M4.79776 37.0816L15.4044 26.475L17.5257 28.5964L6.91908 39.203L4.79776 37.0816Z"/>
        <path d="M26.7178 28.5963L35.2031 37.0815L33.0818 39.2029L22.4752 28.5963H26.7178Z"/>
        <path d="M6.91885 8.79727L17.5255 19.4039L15.4041 21.5252L4.79753 10.9186L6.91885 8.79727Z"/>
        <path d="M18.5 22.5L40 22.5V25.5L18.5 25.5V22.5Z"/>
        <path d="M2.62269e-07 22.5L15 22.5V25.5L0 25.5L2.62269e-07 22.5Z"/>
    </svg>
    @if ($text)
        <span class="flex items-start gap-1">
            <span class="{{ $large ? 'text-heading-sm' : 'text-subheading' }} font-semibold leading-none tracking-tight">Habitude</span>
            @if ($label)
                <span class="-mt-1.5 rounded-md bg-ink px-1.5 py-0.5 text-[10px] font-semibold uppercase leading-none tracking-wide text-paper">{{ $label }}</span>
            @endif
        </span>
    @endif
</span>
