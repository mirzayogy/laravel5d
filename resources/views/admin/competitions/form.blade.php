@extends('layouts.app')

@section('title', $competition ? 'Edit competition' : 'New competition')

@section('content')
    <x-page-header :title="$competition ? 'Edit competition' : 'New competition'" icon="flag"
                   :crumbs="['Admin' => route('admin.dashboard'), 'Competitions' => route('admin.competitions.index'), ($competition ? 'Edit' : 'New') => null]" />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <form method="POST" action="{{ $competition ? route('admin.competitions.update', $competition) : route('admin.competitions.store') }}" class="space-y-4">
                @csrf
                @if ($competition)
                    @method('PUT')
                @endif

                <x-input name="title" label="Title" :value="$competition?->title" required />
                <x-input name="slug" label="Slug (optional)" :value="$competition?->slug" hint="Leave empty to generate it from the title." class="font-mono" />
                <x-textarea name="description" label="Description (Markdown)" :value="$competition?->description" rows="6" />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input name="start_at" type="datetime-local" label="Start" :value="$competition?->start_at?->format('Y-m-d\TH:i')" required />
                    <x-input name="end_at" type="datetime-local" label="End" :value="$competition?->end_at?->format('Y-m-d\TH:i')" required />
                </div>
                <x-select name="status" label="Status">
                    @foreach (['draft' => 'Draft (hidden from players)', 'running' => 'Published (starts and ends automatically by time)', 'finished' => 'Ended early (closed now)'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $competition?->status ?? 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select>
                <div class="flex flex-wrap gap-2">
                    <x-button type="submit" icon="check">Save competition</x-button>
                    <x-button :href="route('admin.competitions.index')" variant="ghost">Cancel</x-button>
                </div>
            </form>
        </x-card>

        <div class="space-y-6">
            <x-card tone="sky">
                <h2 class="font-extrabold">How phases work</h2>
                <ul class="mt-2 space-y-2 text-sm text-slate-700">
                    <li class="flex gap-2"><x-badge tone="amber">Draft</x-badge> Only admins can see it.</li>
                    <li class="flex gap-2"><x-badge tone="sky">Upcoming</x-badge> Published, before the start. Teams can register.</li>
                    <li class="flex gap-2"><x-badge tone="green">Live</x-badge> Between start and end. Flags are accepted.</li>
                    <li class="flex gap-2"><x-badge tone="slate">Finished</x-badge> After the end, or ended early.</li>
                </ul>
            </x-card>
            @if ($competition)
                <x-card>
                    <h2 class="font-extrabold">Current phase</h2>
                    <div class="mt-2"><x-phase-badge :phase="$competition->phase" class="text-sm" /></div>
                    <x-button :href="route('admin.challenges.index', ['competition' => $competition->id])" size="sm" variant="soft" class="mt-3" icon="target">Manage its challenges</x-button>
                </x-card>
            @endif
        </div>
    </div>
@endsection
