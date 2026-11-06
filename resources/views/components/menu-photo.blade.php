@props(['menu', 'icon' => '🍽️'])

@php
    $url = $menu->image_url;
    $hue = crc32($menu->name) % 360;
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $menu->name }}" loading="lazy" {{ $attributes->merge(['class' => 'object-cover']) }}>
@else
    <div role="img" aria-label="{{ $menu->name }}" {{ $attributes->merge(['class' => 'grid place-items-center']) }}
         style="background: linear-gradient(135deg, hsl({{ $hue }} 45% 91%), hsl({{ ($hue + 40) % 360 }} 50% 82%))">
        <span class="text-4xl drop-shadow-sm">{{ $icon }}</span>
    </div>
@endif
