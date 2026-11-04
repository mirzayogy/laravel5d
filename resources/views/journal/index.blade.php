@php
    $view = request('view') === 'grid' ? 'grid' : 'list';
    $count = $entries->flatten(1)->count();
@endphp

<x-app-layout title="{{ __('Journal') }}" :crumbs="[[__('Track')], [__('Journal')]]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">{{ __('Journal') }} <span class="font-normal text-pewter">({{ $count }})</span></h1>
            <p class="text-body-sm text-pewter">{{ __('Entries written on a day you rated your mood are linked to it.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <x-rubber-segment :value="$view" label="{{ __('View') }}" :items="[
                ['value' => 'list', 'label' => __('List'), 'icon' => 'list', 'href' => route('journal.index', ['view' => 'list'])],
                ['value' => 'grid', 'label' => __('Grid'), 'icon' => 'layout-grid', 'href' => route('journal.index', ['view' => 'grid'])],
            ]" />
            <a href="{{ route('journal.create') }}" class="btn btn-primary"><x-icon name="plus" /> {{ __('New entry') }}</a>
        </div>
    </div>

    @forelse ($entries as $month => $items)
        <details class="group mb-4" open>
            <summary class="mb-2 flex cursor-pointer list-none items-center gap-2 text-body-sm font-semibold [&::-webkit-details-marker]:hidden">
                <x-icon name="chevron-right" class="size-4 text-steel transition-transform group-open:rotate-90" />
                {{ $month }} <span class="font-normal text-pewter">{{ $items->count() }}</span>
            </summary>

            @if ($view === 'list')
                <div class="card divide-y divide-mist/40 overflow-hidden">
                    @foreach ($items as $entry)
                        <x-swipe-row :edit-url="route('journal.edit', $entry)" :delete-id="'delete-journal-'.$entry->id">
                            <a href="{{ route('journal.edit', $entry) }}" class="flex min-w-0 flex-1 items-center gap-3 px-6 py-3">
                                <span class="flex size-10 flex-none flex-col items-center justify-center rounded-xl bg-fog leading-none">
                                    <span class="text-caption text-pewter">{{ $entry->entry_date->translatedFormat('M') }}</span>
                                    <span class="text-body-sm font-semibold">{{ $entry->entry_date->format('j') }}</span>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate font-medium text-ink">{{ $entry->title }}</span>
                                    <span class="block truncate text-caption text-pewter">{{ \Illuminate\Support\Str::limit($entry->content, 90) }}</span>
                                </span>
                                @if ($entry->moodEntry)
                                    <span class="badge flex-none"><x-icon name="smile" class="size-3" />{{ __(config('habitude.moods')[$entry->moodEntry->mood_level]) }}</span>
                                @endif
                            </a>
                        </x-swipe-row>
                    @endforeach
                </div>
            @else
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $entry)
                        <a href="{{ route('journal.edit', $entry) }}" class="card tile block p-5 hover:bg-fog">
                            <span class="flex items-center justify-between text-caption text-pewter">
                                {{ $entry->entry_date->translatedFormat('D, j M') }}
                                @if ($entry->moodEntry) <span class="badge">{{ __(config('habitude.moods')[$entry->moodEntry->mood_level]) }}</span> @endif
                            </span>
                            <span class="mt-2 block truncate text-body font-semibold">{{ $entry->title }}</span>
                            <span class="mt-1 line-clamp-3 text-body-sm text-graphite">{{ $entry->content }}</span>
                        </a>
                    @endforeach
                </div>
            @endif

            @foreach ($items as $entry)
                <x-delete-modal id="delete-journal-{{ $entry->id }}" :action="route('journal.destroy', $entry)" title="{{ __('Delete journal entry') }}"
                    :message="__('Delete “:name”?', ['name' => $entry->title])" />
            @endforeach
        </details>
    @empty
        <div class="card px-6 py-16 text-center text-pewter">
            {{ __('Nothing written yet.') }} <a href="{{ route('journal.create') }}" class="font-semibold text-ink underline">{{ __('Start your first entry') }}</a>.
        </div>
    @endforelse
</x-app-layout>
