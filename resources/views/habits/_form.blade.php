@php
    $habit ??= null;
    $selectedTags = collect(old('tags', $habit?->tags->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id);
    $categoryOptions = $categories->map(fn ($c) => ['value' => $c->id, 'label' => trim($c->icon.' '.$c->display_name), 'dot' => $c->color])->values()->all();
    $unitOptions = collect($units)->map(fn ($u) => ['value' => $u, 'label' => __($u)])->all();
    $frequencyOptions = collect(range(0, 6))->map(fn ($n) => ['value' => (string) $n, 'label' => $n === 0 ? __('Every day') : trans_choice('{1} :n day a week|[2,*] :n days a week', $n, ['n' => $n])])->all();
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="label">{{ __('Name') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name', $habit?->name) }}"
            placeholder="{{ __('e.g. Drink water') }}" class="input @error('name') input-error @enderror" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="label">{{ __('Category') }}</label>
        <x-select name="category_id" :options="$categoryOptions" :value="old('category_id', $habit?->category_id)" placeholder="{{ __('Choose a category') }}" label="{{ __('Category') }}" />
        @error('category_id') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="label">{{ __('Description') }} <span class="font-normal text-pewter">{{ __('(optional)') }}</span></label>
        <textarea id="description" name="description" rows="3" maxlength="500"
            class="input @error('description') input-error @enderror">{{ old('description', $habit?->description) }}</textarea>
        @error('description') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="target_count" class="label">{{ __('Daily target') }}</label>
            <input type="number" id="target_count" name="target_count" min="1" max="100000"
                value="{{ old('target_count', $habit?->target_count ?? 1) }}"
                class="input @error('target_count') input-error @enderror" required>
            @error('target_count') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">{{ __('Unit') }}</label>
            <x-select name="unit" :options="$unitOptions" :value="old('unit', $habit?->unit ?? 'times')" label="{{ __('Unit') }}" />
            <p class="help">{{ __('Habits in minutes get a timer on the dashboard.') }}</p>
            @error('unit') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="label">{{ __('How often') }}</label>
        <x-select name="weekly_goal" :options="$frequencyOptions" :value="(string) old('weekly_goal', $habit?->weekly_goal ?? 0)" label="{{ __('How often') }}" />
        <p class="help">{{ __('A weekly goal keeps your streak alive as long as you reach it each week (Monday to Sunday).') }}</p>
        @error('weekly_goal') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <fieldset>
        <legend class="label">{{ __('Tags') }} <span class="font-normal text-pewter">{{ __('(optional)') }}</span></legend>
        @if ($tags->isEmpty())
            <p class="help">No tags yet.@if (auth()->user()->isAdmin()) <a href="{{ route('admin.tags.create') }}" class="text-ink underline">{{ __('Create one') }}</a>.@else An administrator manages the tag list.@endif</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="peer sr-only" @checked($selectedTags->contains($tag->id))>
                        <span class="inline-flex items-center rounded-md border border-mist bg-paper px-3 py-1.5 text-body-sm text-graphite transition-colors peer-checked:border-ink peer-checked:bg-ink peer-checked:text-paper peer-focus-visible:outline-2 peer-focus-visible:outline-ember">
                            {{ $tag->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
        @error('tags.*') <p class="field-error">{{ $message }}</p> @enderror
    </fieldset>

    <div class="rounded-xl border border-mist/60 px-4 py-2">
        <x-spring-check name="is_active" label="{{ __('Active') }}" :checked="old('is_active', $habit?->is_active ?? true)" :large="true" />
        <p class="pb-2 text-caption text-pewter">{{ __('Paused habits stay in your list but are not shown on the dashboard.') }}</p>
    </div>
</div>
