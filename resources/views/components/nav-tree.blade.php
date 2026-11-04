@props(['items'])

@php
    // Geometry shared with the React Bits original: row height, indent, trunk x and corner radius.
    $pad = 6; $row = 36; $trunk = 14; $indent = 40; $radius = min(10, $row / 2 - 2); $endX = $indent - 8;
    $rowY = fn ($k) => $pad + $k * $row + $row / 2;
    $branch = fn ($k) => "M {$trunk} ".($rowY($k) - $radius)." A {$radius} {$radius} 0 0 0 ".($trunk + $radius)." {$rowY($k)} H {$endX}";
    $reach = fn ($k) => "M {$trunk} 0 V ".($rowY($k) - $radius)." A {$radius} {$radius} 0 0 0 ".($trunk + $radius)." {$rowY($k)} H {$endX}";
    $length = fn ($k) => round($rowY($k) - $radius + (M_PI * $radius) / 2 + ($endX - $trunk - $radius), 2);
@endphp

<nav class="bm" data-branched-menu aria-label="{{ __('Main navigation') }}">
    <span class="bm-marker" data-bm-marker aria-hidden="true"></span>

    @foreach ($items as $item)
        @if (empty($item['children']))
            <a href="{{ $item['url'] }}" class="bm-head" @if ($item['active']) aria-current="page" @endif>{{ $item['label'] }}</a>
        @else
            @php
                $kids = $item['children'];
                $open = $item['open'] ?? collect($kids)->contains('active', true);
                $bodyH = $pad * 2 + count($kids) * $row;
            @endphp
            <div class="bm-section" data-bm-section @if ($open) data-open @endif>
                <button type="button" class="bm-head" data-bm-toggle aria-expanded="{{ $open ? 'true' : 'false' }}">{{ $item['label'] }}</button>
                <div class="bm-fold">
                    <div class="bm-clip">
                        <div class="bm-body" style="height: {{ $bodyH }}px">
                            <svg class="bm-svg" width="{{ $indent }}" height="{{ $bodyH }}" aria-hidden="true">
                                <path d="M {{ $trunk }} 0 V {{ $rowY(count($kids) - 1) - $radius }}"/>
                                @foreach ($kids as $k => $kid)
                                    <path d="{{ $branch($k) }}"/>
                                @endforeach
                                @foreach ($kids as $k => $kid)
                                    <path class="bm-reach" d="{{ $reach($k) }}" style="--len: {{ $length($k) }}" @if ($kid['active']) data-active @endif/>
                                @endforeach
                            </svg>
                            @foreach ($kids as $kid)
                                <a href="{{ $kid['url'] }}" class="bm-item" @if ($kid['active']) aria-current="page" @endif tabindex="{{ $open ? 0 : -1 }}">
                                    @isset($kid['icon'])<x-icon :name="$kid['icon']" />@endisset
                                    <span>{{ $kid['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</nav>
