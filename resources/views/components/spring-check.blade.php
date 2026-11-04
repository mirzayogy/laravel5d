@props(['label' => '', 'checked' => false, 'name' => null, 'value' => '1', 'large' => false, 'ariaLabel' => null])

<span class="inline-flex">
    <button type="button" role="checkbox" aria-checked="{{ $checked ? 'true' : 'false' }}" data-spring-check
        @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif
        {{ $attributes->class(['sc', 'sc-large' => $large]) }}>
        <span class="sc-box">
            <span class="sc-inner">
                <span class="sc-ring"></span>
                <span class="sc-fill"></span>
                <svg class="sc-tick" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5" pathLength="1"/></svg>
            </span>
        </span>
        @if ($label !== '')
            <span class="sc-text"><span class="sc-word">{{ $label }}</span><span class="sc-rule"></span></span>
        @endif
    </button>
    @if ($name)
        <input type="hidden" data-sc-input name="{{ $name }}" value="{{ $value }}" @disabled(! $checked)>
    @endif
</span>
