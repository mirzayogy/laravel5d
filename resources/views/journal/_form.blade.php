@php
    $journal ??= null;
    $prompt ??= null;
    $date ??= null;
@endphp

<div class="space-y-5">
    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label for="entry_date" class="label">{{ __('Date') }}</label>
            <input type="date" id="entry_date" name="entry_date" max="{{ $today->toDateString() }}"
                value="{{ old('entry_date', $journal?->entry_date->toDateString() ?? ($date ?? $today)->toDateString()) }}"
                class="input @error('entry_date') input-error @enderror" required>
            @error('entry_date') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="title" class="label">{{ __('Title') }}</label>
            <input type="text" id="title" name="title" maxlength="120" value="{{ old('title', $journal?->title ?? ($prompt ? ($date ?? $today)->translatedFormat('l, j F') : null)) }}"
                placeholder="{{ __('A good day') }}" class="input @error('title') input-error @enderror" required>
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
    @if ($prompt)
        <div class="flex gap-3 rounded-xl border border-mist/60 bg-fog px-4 py-3">
            <x-icon name="book-open" class="mt-0.5 size-4 flex-none text-ember" />
            <div>
                <p class="text-caption text-pewter">{{ __('A question to start with') }}</p>
                <p class="text-body-sm font-medium text-ink">{{ $prompt }}</p>
            </div>
        </div>
    @endif
    <div>
        <label for="content" class="label">{{ __('Entry') }}</label>
        <textarea id="content" name="content" rows="9" maxlength="5000" @if ($prompt) placeholder="{{ $prompt }}" autofocus @endif class="input @error('content') input-error @enderror" required>{{ old('content', $journal?->content) }}</textarea>
        @error('content') <p class="field-error">{{ $message }}</p> @enderror
    </div>
    @if ($journal?->moodEntry)
        <p class="flex items-center gap-2 text-body-sm text-pewter"><x-icon name="smile" class="size-4" /> {{ __('Linked to your mood that day:') }} <strong class="text-ink">{{ __(config('habitude.moods')[$journal->moodEntry->mood_level]) }}</strong></p>
    @else
        <p class="help">{{ __('If you rated your mood on this date, the entry links to it automatically.') }}</p>
    @endif
</div>
