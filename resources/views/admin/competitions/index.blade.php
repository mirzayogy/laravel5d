@extends('layouts.app')

@section('title', 'Manage competitions')

@section('content')
    <x-page-header title="Competitions" subtitle="Create competitions and decide when they are visible." icon="flag">
        <x-slot:actions>
            <x-button :href="route('admin.competitions.create')" variant="accent" icon="plus">New competition</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.admin-nav')

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.competitions.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-56 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by title" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <select name="status" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">Any status</option>
                @foreach (['draft' => 'Draft', 'running' => 'Published', 'finished' => 'Ended early'] as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-button type="submit" icon="search">Filter</x-button>
        </form>
    </x-card>

    <x-card :padded="false" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50 text-left text-xs font-bold tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Competition</th>
                        <th class="px-4 py-3">Phase</th>
                        <th class="px-4 py-3">Period</th>
                        <th class="px-4 py-3 text-right">Challenges</th>
                        <th class="px-4 py-3 text-right">Teams</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($competitions as $competition)
                        <tr class="border-t border-sky-50 hover:bg-sky-50/50">
                            <td class="px-4 py-3">
                                <div class="font-bold">{{ $competition->title }}</div>
                                <div class="font-mono text-xs text-slate-400">{{ $competition->slug }}</div>
                            </td>
                            <td class="px-4 py-3"><x-phase-badge :phase="$competition->phase" /></td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $competition->start_at->format('d M Y H:i') }}<br>{{ $competition->end_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $competition->challenges_count }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $competition->teams_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <x-button :href="route('competitions.show', $competition)" size="sm" variant="soft" icon="eye">View</x-button>
                                    <x-button :href="route('admin.competitions.edit', $competition)" size="sm" variant="ghost" icon="edit">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.competitions.destroy', $competition) }}" onsubmit="return confirm('Delete this competition and all of its challenges?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" size="sm" variant="danger" icon="trash">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No competition found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-6">{{ $competitions->links('pagination.custom') }}</div>
@endsection
