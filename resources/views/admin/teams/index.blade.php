@extends('layouts.app')

@section('title', 'Manage teams')

@section('content')
    <x-page-header title="Teams" subtitle="Teams that are registered on Flagboard." icon="users" />

    @include('partials.admin-nav')

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.teams.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-56 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search teams" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <x-button type="submit" icon="search">Search</x-button>
        </form>
    </x-card>

    <x-card :padded="false" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50 text-left text-xs font-bold tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Team</th>
                        <th class="px-4 py-3">Captain</th>
                        <th class="px-4 py-3 text-right">Members</th>
                        <th class="px-4 py-3 text-right">Events</th>
                        <th class="px-4 py-3 text-right">Solves</th>
                        <th class="px-4 py-3">Invite code</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teams as $team)
                        <tr class="border-t border-sky-50 hover:bg-sky-50/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3"><x-avatar :name="$team->name" size="sm" /><a href="{{ route('teams.show', $team) }}" class="font-bold hover:text-sky-700">{{ $team->name }}</a></div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $team->captain?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $team->users_count }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $team->competitions_count }}</td>
                            <td class="px-4 py-3 text-right font-bold tabular-nums">{{ $team->solves_count }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $team->invite_code }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Delete {{ $team->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" size="sm" variant="danger" icon="trash">Delete</x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">No team found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-6">{{ $teams->links('pagination.custom') }}</div>
@endsection
