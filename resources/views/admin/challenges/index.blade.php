@extends('layouts.app')

@section('title', 'Manage challenges')

@section('content')
    <x-page-header title="Challenges" subtitle="Write the puzzles, set the points, and publish when ready." icon="target">
        <x-slot:actions>
            <x-button :href="route('admin.challenges.create')" variant="accent" icon="plus">New challenge</x-button>
        </x-slot:actions>
    </x-page-header>

    @include('partials.admin-nav')

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.challenges.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-52 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search by title" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <select name="competition" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">All competitions</option>
                @foreach ($competitions as $competition)
                    <option value="{{ $competition->id }}" @selected((int) $filters['competition'] === $competition->id)>{{ $competition->title }}</option>
                @endforeach
            </select>
            <select name="category" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) $filters['category'] === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="difficulty" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">Any difficulty</option>
                @foreach (['easy', 'medium', 'hard'] as $level)
                    <option value="{{ $level }}" @selected($filters['difficulty'] === $level)>{{ ucfirst($level) }}</option>
                @endforeach
            </select>
            <select name="published" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">Published or not</option>
                <option value="1" @selected($filters['published'] === '1')>Published</option>
                <option value="0" @selected($filters['published'] === '0')>Draft</option>
            </select>
            <x-button type="submit" icon="search">Filter</x-button>
        </form>
    </x-card>

    <x-card :padded="false" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50 text-left text-xs font-bold tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Challenge</th>
                        <th class="px-4 py-3">Competition</th>
                        <th class="px-4 py-3">Level</th>
                        <th class="px-4 py-3 text-right">Points</th>
                        <th class="px-4 py-3 text-right">Hints</th>
                        <th class="px-4 py-3 text-right">Solves</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($challenges as $challenge)
                        <tr class="border-t border-sky-50 hover:bg-sky-50/50">
                            <td class="px-4 py-3">
                                <div class="font-bold">{{ $challenge->title }}</div>
                                <x-category-badge :name="$challenge->category->name" class="mt-1" />
                            </td>
                            <td class="max-w-48 truncate px-4 py-3 text-slate-600">{{ $challenge->competition->title }}</td>
                            <td class="px-4 py-3"><x-difficulty :level="$challenge->difficulty" /></td>
                            <td class="px-4 py-3 text-right font-bold tabular-nums">{{ $challenge->points }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $challenge->hints_count }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $challenge->solves_count }}</td>
                            <td class="px-4 py-3"><x-badge :tone="$challenge->is_published ? 'green' : 'amber'">{{ $challenge->is_published ? 'Published' : 'Draft' }}</x-badge></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <x-button :href="route('admin.challenges.edit', $challenge)" size="sm" variant="ghost" icon="edit">Edit</x-button>
                                    <form method="POST" action="{{ route('admin.challenges.destroy', $challenge) }}" onsubmit="return confirm('Delete this challenge with its hints and submissions?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" size="sm" variant="danger" icon="trash">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">No challenge found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-6">{{ $challenges->links('pagination.custom') }}</div>
@endsection
