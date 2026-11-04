@php
    $achievement ??= null;
    $criteriaOptions = collect(config('habitude.criteria'))->map(fn ($l, $v) => ['value' => $v, 'label' => $l])->values()->all();
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="label">{{ __('Name') }}</label>
        <input type="text" id="name" name="name" maxlength="60" value="{{ old('name', $achievement?->name) }}" placeholder="{{ __('e.g. 7-Day Streak') }}"
            class="input @error('name') input-error @enderror" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="description" class="label">{{ __('Description') }} <span class="font-normal text-pewter">{{ __('(optional)') }}</span></label>
        <input type="text" id="description" name="description" maxlength="255" value="{{ old('description', $achievement?->description) }}" class="input">
        @error('description') <p class="field-error">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="label">{{ __('Unlocked by') }}</label>
            <x-select name="criteria_type" :options="$criteriaOptions" :value="old('criteria_type', $type ?? null)" placeholder="{{ __('Choose a goal type') }}" label="{{ __('Goal type') }}" />
            @error('criteria_type') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="criteria_threshold" class="label">{{ __('How many') }}</label>
            <input type="number" id="criteria_threshold" name="criteria_threshold" min="1" value="{{ old('criteria_threshold', $threshold ?? 7) }}"
                class="input @error('criteria_threshold') input-error @enderror" required>
            @error('criteria_threshold') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
