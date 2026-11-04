@php($category ??= null)

<div class="space-y-5">
    <div>
        <label for="name" class="label">{{ __('Name') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name', $category?->name) }}"
            placeholder="{{ __('e.g. Stay Hydrated') }}" class="input @error('name') input-error @enderror" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="icon" class="label">{{ __('Icon (emoji)') }}</label>
            <input type="text" id="icon" name="icon" value="{{ old('icon', $category?->icon) }}"
                placeholder="💧" maxlength="8" class="input @error('icon') input-error @enderror">
            @error('icon') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="color" class="label">{{ __('Color') }}</label>
            <div class="flex items-center gap-3">
                <input type="color" id="color" name="color" value="{{ old('color', $category?->color ?? '#6366f1') }}"
                    data-color-input="#color-hex" class="h-10 w-14 cursor-pointer rounded-xl border border-mist bg-paper p-1">
                <span id="color-hex" class="text-body-sm text-pewter"></span>
            </div>
            @error('color') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
