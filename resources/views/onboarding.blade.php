<x-guest-layout title="{{ __('Welcome') }}" :wide="true">
    <h1 class="text-heading-sm">{{ __('Let\'s set up Habitude') }}</h1>
    <p class="mt-1 text-body-sm text-pewter">{{ __('Four quick steps. You can change everything later.') }}</p>

    <form action="{{ route('onboarding.store') }}" method="POST" class="mt-6">
        @csrf

        <div class="stp" data-stepper>
            <div class="stp-track" role="tablist" aria-label="{{ __('Setup steps') }}">
                <span class="stp-glider" aria-hidden="true"></span>
                @foreach ([__('Profile'), __('Habits'), __('Reminder'), __('Finish')] as $i => $step)
                    <button type="button" class="stp-seg" role="tab" data-step-seg>
                        <span class="stp-badge">{{ $i + 1 }}</span>
                        <span class="stp-title max-sm:hidden">{{ $step }}</span>
                    </button>
                @endforeach
            </div>

            <div class="stp-viewport mt-6">
                <div class="stp-panes">
                    {{-- 1. Profile --}}
                    <section class="stp-pane" data-step-pane aria-label="{{ __('Profile') }}">
                        <h2 class="text-subheading">{{ __('About your day') }}</h2>
                        <p class="mt-1 text-body-sm text-pewter">{{ __('Your timezone decides when a new day starts for your streak.') }}</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="label">{{ __('Timezone') }}</label>
                                <x-select name="timezone" label="{{ __('Timezone') }}" :value="old('timezone', 'Asia/Makassar')"
                                    :options="collect(config('habitude.timezones'))->map(fn ($l, $v) => ['value' => $v, 'label' => $l])->values()->all()" />
                                @error('timezone') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="daily_goal" class="label">{{ __('Habits to finish per day') }}</label>
                                <input type="number" id="daily_goal" name="daily_goal" min="1" max="20" value="{{ old('daily_goal', 3) }}" class="input" required>
                                @error('daily_goal') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </section>

                    {{-- 2. Starter habits --}}
                    <section class="stp-pane" data-step-pane aria-label="{{ __('Habits') }}">
                        <h2 class="text-subheading">{{ __('Pick a few starter habits') }}</h2>
                        <p class="mt-1 text-body-sm text-pewter">{{ __('Optional. Choose what sounds doable, you can add your own later.') }}</p>
                        <div class="mt-5 max-h-72 space-y-5 overflow-y-auto pr-1">
                            @foreach ($categories as $category)
                                <fieldset>
                                    <legend class="mb-1 flex items-center gap-2 text-body-sm font-semibold">
                                        <span class="size-3 rounded-md" style="background: {{ $category->color }}"></span>
                                        {{ $category->icon }} {{ $category->display_name }}
                                    </legend>
                                    <div class="grid sm:grid-cols-2">
                                        @foreach (config('habitude.starters')[$category->slug] as $i => [$name, $target, $unit])
                                            <x-spring-check name="starters[]" value="{{ $category->slug }}|{{ $i }}"
                                                :label="__($name).' · '.$target.' '.__($target === 1 ? \Illuminate\Support\Str::singular($unit) : $unit)" :checked="in_array($category->slug.'|'.$i, old('starters', []))" />
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>
                    </section>

                    {{-- 3. Reminder --}}
                    <section class="stp-pane" data-step-pane aria-label="{{ __('Reminder') }}">
                        <h2 class="text-subheading">{{ __('Want a daily nudge?') }}</h2>
                        <p class="mt-1 text-body-sm text-pewter">{{ __('We add one reminder to every habit you picked. Leave it empty to skip.') }}</p>
                        <div class="mt-5 max-w-xs">
                            <label for="reminder_time" class="label">{{ __('Reminder time') }}</label>
                            <input type="time" id="reminder_time" name="reminder_time" value="{{ old('reminder_time', '08:00') }}" class="input">
                            @error('reminder_time') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </section>

                    {{-- 4. Finish --}}
                    <section class="stp-pane" data-step-pane aria-label="{{ __('Finish') }}">
                        <h2 class="text-subheading">{{ __('You are ready') }}</h2>
                        <p class="mt-1 text-body-sm text-pewter">{{ __('Your dashboard shows today\'s habits, a calendar, and your streak. Check a habit off to see it spring.') }}</p>
                        <ul class="mt-5 space-y-2 text-body-sm text-graphite">
                            <li class="flex items-center gap-2"><x-icon name="circle-check" class="size-4 text-ink" /> {{ __('Tap the box next to a habit to complete it') }}</li>
                            <li class="flex items-center gap-2"><x-icon name="circle-check" class="size-4 text-ink" /> {{ __('Rate your mood once a day') }}</li>
                            <li class="flex items-center gap-2"><x-icon name="circle-check" class="size-4 text-ink" /> {{ __('Unlock achievements as your streak grows') }}</li>
                        </ul>
                    </section>
                </div>
            </div>

            <div class="stp-bars mt-6" aria-hidden="true">
                @foreach (range(1, 4) as $i)
                    <span class="stp-bar" data-step-bar><span></span></span>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-between">
                <button type="button" class="btn btn-outline" data-step-back hidden>{{ __('Back') }}</button>
                <span class="flex-1"></span>
                <button type="button" class="btn btn-primary" data-step-next>{{ __('Next') }} <x-icon name="arrow-right" /></button>
                <button type="submit" class="btn btn-primary" data-step-submit hidden>{{ __('Start tracking') }}</button>
            </div>
        </div>
    </form>
</x-guest-layout>
