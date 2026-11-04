@php
    $isToday = $date->equalTo($today);
    $prev = $month->subMonth()->format('Y-m');
    $next = $month->addMonth();
    $canNext = $next->startOfMonth() <= $today->startOfMonth();
    $weekdays = array_map(fn ($d) => __($d), ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
    $moodLabels = array_map(fn ($l) => __($l), array_values(config('habitude.moods')));
@endphp

<x-app-layout title="{{ __('Dashboard') }}" :crumbs="[[__('Dashboard')]]">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        @if ($isToday)
            {{-- A different welcome on every visit, built from today's open habits --}}
            <div class="greet flex items-center gap-4">
                <x-avatar :user="auth()->user()" size="size-14 sm:size-16" />
                <div>
                    <h1 class="text-[28px] font-semibold leading-tight tracking-tight sm:text-[34px]">{{ $greeting['hello'] }}</h1>
                    <p class="text-subheading text-graphite">{{ $greeting['line'] }}</p>
                    <p class="mt-1 text-caption text-pewter">{{ $date->translatedFormat('l, j F Y') }}</p>
                </div>
            </div>
        @else
            <div>
                <h1 class="text-heading-sm">{{ $date->translatedFormat('l') }}, {{ $date->translatedFormat('j F Y') }}</h1>
                <p class="text-body-sm text-pewter">
                    {{ __('You are looking at a past day. You can still log it.') }}
                    <a href="{{ route('dashboard') }}" class="font-semibold text-ink underline">{{ __('Back to today') }}</a>
                </p>
            </div>
        @endif
        <button type="button" class="btn btn-primary" data-modal-open="quick-add"><x-icon name="plus" /> {{ __('Add habit') }}</button>
    </div>
    <x-quick-add />

    @if ($missedYesterday)
        <div data-nudge="missed-{{ $yesterday->toDateString() }}" class="mb-6 flex flex-wrap items-center gap-3 rounded-xl border border-mist/60 bg-fog px-4 py-3 text-body-sm">
            <x-icon name="calendar" class="size-4 flex-none text-ember" />
            <p class="flex-1 text-graphite">{{ __('Nothing was checked off on') }} <strong class="text-ink">{{ $yesterday->translatedFormat('l, j F') }}</strong>{{ __('. Want to log it?') }}</p>
            <a href="{{ route('dashboard', ['date' => $yesterday->toDateString()]) }}" class="btn btn-outline btn-sm">{{ __('Log yesterday') }}</a>
            @if ($restYesterdayFree)
                <form action="{{ route('rest-days.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $yesterday->toDateString() }}">
                    <button type="submit" class="btn btn-outline btn-sm"><x-icon name="moon" class="size-3.5" /> {{ __('It was a rest day') }}</button>
                </form>
            @endif
            <button type="button" class="btn btn-ghost btn-sm" data-nudge-dismiss>{{ __('Not now') }}</button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="flame" class="size-4 text-ember" /> {{ __('Current streak') }}</p>
            <p class="mt-2 flex items-baseline gap-1.5 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="streak" :value="$streak['current']" /><span class="text-body-sm font-normal text-pewter">{{ __('days') }}</span></p>
            @if ($isToday)
                @if ($restToday)
                    <form action="{{ route('rest-days.destroy', $today->toDateString()) }}" method="POST" class="mt-2 flex items-center gap-1.5 text-caption text-pewter">
                        @csrf @method('DELETE')
                        <x-icon name="moon" class="size-3.5" /> {{ __('Resting today.') }}
                        <button type="submit" class="font-semibold text-ink underline">{{ __('Undo') }}</button>
                    </form>
                @elseif (! $restThisWeek)
                    <form action="{{ route('rest-days.store') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-caption text-pewter hover:text-ink" title="{{ __('A rest day keeps your streak safe. One per week.') }}"><x-icon name="moon" class="size-3.5" /> {{ __('Take a rest day') }}</button>
                    </form>
                @else
                    <p class="mt-2 flex items-center gap-1.5 text-caption text-pewter"><x-icon name="moon" class="size-3.5" /> {{ __('Rest day used on :day', ['day' => $restThisWeek->rest_date->translatedFormat('l')]) }}</p>
                @endif
            @endif
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="trophy" class="size-4" /> {{ __('Best streak') }}</p>
            <p class="mt-2 flex items-baseline gap-1.5 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="best" :value="$streak['best']" /><span class="text-body-sm font-normal text-pewter">{{ __('days') }}</span></p>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="circle-check" class="size-4" /> {{ $isToday ? __('Done today') : __('Done that day') }}</p>
            <p class="mt-2 flex items-baseline gap-1 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="done" :value="$doneCount" /><span class="text-heading-sm font-normal text-steel">/</span><x-counter class="text-heading-sm text-steel" data-stat="total" :value="$rows->count()" /></p>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="calendar" class="size-4" /> {{ __(':month completion', ['month' => $month->translatedFormat('F')]) }}</p>
            <p class="mt-2 flex items-baseline gap-1 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="rate" :value="$monthRate" /><span class="text-heading-sm font-normal text-steel">%</span></p>
        </div>
    </div>

    {{-- A year of check-ins: GitHub-style heat map that folds up into a 3D skyline --}}
    @php
        $skylineOptions = [
            'endDate' => $today->toDateString(),
            'unit' => 'check-in',
            'locale' => str_replace('_', '-', app()->getLocale() === 'id' ? 'id-ID' : 'en-US'),
            'weekStart' => 1,
            'href' => route('dashboard'),
            'palette' => ['#ffd1b8', '#ffac80', '#ff8340', '#ff5900'], // one hue, opacity 28% / 50% / 75% / 100%
        ];
    @endphp
    <section class="mb-6" aria-label="{{ __('Your year in check-ins') }}">
        <div data-skyline="{{ json_encode($skylineOptions) }}">
            <script type="application/json">{!! json_encode($activity, JSON_HEX_TAG) !!}</script>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Today's habits: filter, and a row limit the user can change --}}
        <section class="card lg:col-span-2" aria-labelledby="habits-title" data-today data-today-date="{{ $today->toDateString() }}">
            <div class="card-header">
                <div>
                    <h2 id="habits-title" class="text-subheading">{{ $isToday ? __("Today's habits") : __('Habits') }}</h2>
                    <p class="text-body-sm text-pewter">{{ __('Tap the box to complete a habit.') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-rubber-segment size="sm" value="all" label="{{ __('Filter habits') }}" :items="[
                        ['value' => 'all', 'label' => __('All'), 'count' => $rows->count()],
                        ['value' => 'todo', 'label' => __('To do'), 'count' => $rows->where('done', false)->count()],
                        ['value' => 'done', 'label' => __('Done'), 'count' => $rows->where('done', true)->count()],
                    ]" />
                    <div class="w-28" data-today-limit>
                        <x-select name="rows" label="{{ __('Rows to show') }}" value="5" :compact="true" :options="[
                            ['value' => '5', 'label' => __('5 rows')], ['value' => '8', 'label' => __('8 rows')],
                            ['value' => '10', 'label' => __('10 rows')], ['value' => 'all', 'label' => __('All rows')],
                        ]" />
                    </div>
                    <a href="{{ route('habits.index') }}" class="btn btn-outline btn-sm">{{ __('Manage') }}</a>
                </div>
            </div>

            <ul class="divide-y divide-mist/40">
                @forelse ($rows as $r)
                    @php
                        $h = $r['habit'];
                        $multi = $h->target_count > 1;
                        $firstOfGroup = $loop->first || $rows[$loop->index - 1]['group'] !== $r['group'];
                    @endphp
                    @if ($firstOfGroup)
                        <li data-today-group="{{ $r['group'] }}" @class(['hidden' => $r['index'] >= 5, 'bg-fog/50 px-6 py-1.5 text-caption font-medium uppercase tracking-wide text-pewter'])>{{ __(['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening', 'anytime' => 'Anytime'][$r['group']]) }}</li>
                    @endif
                    <li data-habit-row data-group="{{ $r['group'] }}" data-name="{{ $h->name }}" data-value="{{ $r['value'] }}" data-url="{{ route('habits.log', $h) }}" data-date="{{ $date->toDateString() }}"
                        data-target="{{ $h->target_count }}" data-unit="{{ $h->unit }}" @if ($r['weekly']) data-goal="{{ $h->weekly_goal }}" @endif data-done="{{ $r['done'] ? 1 : 0 }}"
                        @class(['hidden' => $r['index'] >= 5])>
                        <div class="flex items-center gap-3 px-6 py-3">
                            <x-spring-check :checked="$r['done']" :aria-label="__('Complete :name', ['name' => $h->name])" />
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-2 truncate font-medium text-ink">
                                    <span class="size-2.5 flex-none rounded-md" style="background: {{ $h->category->color }}" aria-hidden="true"></span>
                                    {{ $h->name }}
                                </p>
                                <p class="text-caption text-pewter" data-progress-text>
                                    @if ($multi) {{ $r['value'] }} / {{ $h->target_count }} {{ __($h->unit) }} @else {{ $r['done'] ? __('Done') : __('Not yet') }} @endif
                                </p>
                                @if ($r['weekly'])
                                    <p class="text-caption text-pewter" data-week-text>{{ __(':done of :goal days this week', ['done' => $r['week_done'], 'goal' => $h->weekly_goal]) }}</p>
                                @endif
                                @if ($multi)
                                    <div class="mt-1.5 h-1 max-w-48 overflow-hidden rounded bg-fog">
                                        <span data-progress-bar class="block h-full rounded bg-ember transition-[width] duration-500" style="width: {{ min(100, $r['value'] / $h->target_count * 100) }}%"></span>
                                    </div>
                                @endif
                            </div>

                            @if ($r['time'])
                                <span class="badge" title="{{ __('Reminder time') }}"><x-icon name="clock" class="size-3" />{{ $r['time'] }}</span>
                            @endif

                            <span data-streak-wrap class="badge" @if ($r['streak'] === 0) hidden @endif title="{{ __('Habit streak') }}">
                                <x-icon name="flame" class="size-3.5 text-ember" /><x-counter data-streak :value="$r['streak']" />@if ($r['weekly'])<span class="text-caption text-pewter">{{ __('wk') }}</span>@endif
                            </span>

                            @if ($r['timed'])
                                <button type="button" class="btn btn-outline btn-sm" data-timer-start><x-icon name="timer" class="size-3.5" /> {{ __('Timer') }}</button>
                                <div data-timer-panel hidden class="flex items-center gap-2">
                                    <span class="rounded-md bg-ink px-2 py-1 text-body-sm font-semibold text-paper"><span data-timer-display class="ctr">00:00</span></span>
                                    <button type="button" class="btn btn-primary btn-sm" data-timer-stop>{{ __('Stop') }}</button>
                                    <button type="button" class="btn btn-ghost btn-icon btn-sm" data-timer-cancel aria-label="{{ __('Cancel timer') }}"><x-icon name="x" class="size-4" /></button>
                                </div>
                            @elseif ($multi)
                                <button type="button" class="btn btn-outline btn-sm" data-add aria-label="{{ __('Add one :unit', ['unit' => __(\Illuminate\Support\Str::singular($h->unit))]) }}"><x-icon name="plus" class="size-3.5" /> 1</button>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-pewter">
                        {{ __('No active habits yet.') }} <a href="{{ route('habits.create') }}" class="font-semibold text-ink underline">{{ __('Create one') }}</a>.
                    </li>
                @endforelse
                <li data-today-empty hidden class="px-6 py-10 text-center text-pewter">{{ __('Nothing in this view.') }}</li>
            </ul>

            @if ($rows->isNotEmpty())
                <div class="flex items-center justify-between gap-3 border-t border-mist/60 px-6 py-3 text-caption text-pewter">
                    <span data-today-summary>{{ __('Showing :shown of :total', ['shown' => min(5, $rows->count()), 'total' => $rows->count()]) }}</span>
                    <button type="button" data-today-more @if ($rows->count() <= 5) hidden @endif class="btn btn-ghost btn-sm" aria-expanded="false">{{ __('Show :n more', ['n' => max(0, $rows->count() - 5)]) }}</button>
                </div>
            @endif
        </section>

        {{-- Calendar + mood --}}
        <div class="space-y-6">
            <section class="card" aria-labelledby="cal-title">
                <div class="card-header">
                    <h2 id="cal-title" class="text-subheading">{{ $month->translatedFormat('F Y') }}</h2>
                    <div class="flex gap-1">
                        <a href="{{ route('dashboard', ['month' => $prev, 'date' => $date->toDateString()]) }}" class="btn btn-outline btn-icon" aria-label="{{ __('Previous month') }}"><x-icon name="chevron-left" /></a>
                        @if ($canNext)
                            <a href="{{ route('dashboard', ['month' => $next->format('Y-m'), 'date' => $date->toDateString()]) }}" class="btn btn-outline btn-icon" aria-label="{{ __('Next month') }}"><x-icon name="chevron-right" /></a>
                        @else
                            <span class="btn btn-outline btn-icon opacity-40" aria-hidden="true"><x-icon name="chevron-right" /></span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-2 grid grid-cols-7 gap-1.5 text-center text-caption text-pewter">
                        @foreach ($weekdays as $wd) <span>{{ $wd }}</span> @endforeach
                    </div>
                    <div class="grid grid-cols-7 gap-1.5">
                        @foreach ($weeks as $week)
                            @foreach ($week as $d)
                                @php
                                    $cls = collect(['cal-day', $d['in_month'] ? '' : 'is-out', $d['is_future'] ? 'is-future' : '', $d['is_today'] ? 'is-today' : '', $d['date'] === $date->toDateString() ? 'is-selected' : ''])->filter()->implode(' ');
                                @endphp
                                @if ($d['is_future'])
                                    <span class="{{ $cls }}">{{ $d['day'] }}</span>
                                @else
                                    <a href="{{ route('dashboard', ['date' => $d['date'], 'month' => $month->format('Y-m')]) }}" class="{{ $cls }}"
                                        data-cal-date="{{ $d['date'] }}" style="--r: {{ round($d['ratio'], 2) }}" @if ($d['ratio'] > 0.55) data-high @endif
                                        aria-label="{{ __(':date: :done of :total habits', ['date' => \Carbon\Carbon::parse($d['date'])->translatedFormat('j F'), 'done' => $d['done'], 'total' => $d['total']]) }}">
                                        {{ $d['day'] }}
                                    </a>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-between text-caption text-pewter">
                        <span>{{ __('Less') }}</span>
                        <span class="flex gap-1">
                            @foreach ([0, 0.25, 0.5, 0.75, 1] as $r)
                                <span class="size-3 rounded-md" style="background: color-mix(in srgb, var(--color-ember) {{ $r * 100 }}%, var(--color-fog))"></span>
                            @endforeach
                        </span>
                        <span>{{ __('All done') }}</span>
                    </div>
                </div>
            </section>

            <section class="card" aria-labelledby="mood-title">
                <div class="card-header">
                    <h2 id="mood-title" class="text-subheading">{{ __('How do you feel?') }}</h2>
                    <a href="{{ route('mood.index') }}" class="btn btn-outline btn-sm">{{ __('History') }}</a>
                </div>
                <div class="card-body" data-mood-quick data-url="{{ route('mood.store') }}" data-date="{{ $date->toDateString() }}">
                    <x-peek-rating shape="heart" :value="$mood?->mood_level ?? 0" :labels="$moodLabels" label="{{ __('Mood') }}" />
                    <p class="mt-1 text-caption text-pewter" data-mood-label>
                        {{ $mood ? __('Saved: :label', ['label' => __(config('habitude.moods')[$mood->mood_level])]) : __('Pick one, it saves right away.') }}
                    </p>
                </div>
            </section>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card" aria-labelledby="cat-title">
            <div class="card-header"><h2 id="cat-title" class="text-subheading">{{ __('By category') }}</h2><span class="text-caption text-pewter">{{ $month->translatedFormat('F') }}</span></div>
            <ul class="card-body space-y-3">
                @forelse ($categoryProgress as $c)
                    <li>
                        <div class="mb-1 flex items-center justify-between text-body-sm">
                            <span class="flex items-center gap-2"><span class="size-3 rounded-md" style="background: {{ $c->color }}"></span>{{ $c->icon }} {{ $c->display_name }}</span>
                            <span class="font-semibold">{{ $c->completed }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded bg-fog"><span class="block h-full rounded" style="width: {{ $c->completed / $categoryProgress->max('completed') * 100 }}%; background: {{ $c->color }}"></span></div>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">{{ __('Complete a habit to see progress here.') }}</li>
                @endforelse
            </ul>
        </section>

        <section class="card" aria-labelledby="rem-title">
            <div class="card-header"><h2 id="rem-title" class="text-subheading">{{ __('Reminders') }}</h2><x-icon name="bell" class="size-4 text-steel" /></div>
            <ul class="card-body space-y-3">
                @forelse ($reminders as [$habit, $reminder])
                    <li class="flex items-center justify-between text-body-sm">
                        <span class="truncate">{{ $habit->name }}</span>
                        <span class="badge flex-none"><x-icon name="clock" class="size-3" />{{ \Carbon\Carbon::parse($reminder->remind_at)->format('H:i') }}</span>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">{{ __('No reminders for this day.') }}</li>
                @endforelse
            </ul>
        </section>

        <section class="card" aria-labelledby="ach-title">
            <div class="card-header"><h2 id="ach-title" class="text-subheading">{{ __('Achievements') }}</h2><a href="{{ route('achievements.index') }}" class="btn btn-outline btn-sm">{{ __('All') }}</a></div>
            <ul class="card-body space-y-3">
                @forelse ($recentAchievements as $a)
                    <li class="flex items-center gap-3">
                        <span class="flex size-9 flex-none items-center justify-center rounded-xl bg-ember text-paper"><x-icon name="trophy" /></span>
                        <span class="min-w-0"><span class="block truncate text-body-sm font-semibold">{{ $a->display_name }}</span><span class="block text-caption text-pewter">{{ \Carbon\Carbon::parse($a->pivot->earned_at)->translatedFormat('j M Y') }}</span></span>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">{{ __('Nothing unlocked yet. Check off your first habit!') }}</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-app-layout>
