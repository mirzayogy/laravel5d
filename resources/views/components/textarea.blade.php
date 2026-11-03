@props(['name', 'label' => null, 'value' => null, 'hint' => null, 'rows' => 4])

@php
    $id = $attributes->get('id', $name);
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="mb-1 block text-sm font-semibold text-slate-700">{{ $label }}</label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        {{ $attributes->except('id')->class([
            'w-full rounded-2xl border-2 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none',
            'border-sky-100 focus:border-sky-400' => ! $errors->has($name),
            'border-coral-300 focus:border-coral-500' => $errors->has($name),
        ]) }}>{{ old($name, $value) }}</textarea>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-medium text-coral-700">{{ $message }}</p>
    @enderror
</div>
