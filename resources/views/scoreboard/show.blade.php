@extends('layouts.app')

@section('title', 'Scoreboard')

@section('content')
    <a href="{{ route('competitions.show', $competition) }}" class="text-sm text-indigo-700 underline">{{ $competition->title }}</a>
    <h1 class="mt-1 text-2xl font-semibold">Scoreboard</h1>

    <div class="mt-4 overflow-x-auto rounded border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-slate-600">
                <tr>
                    <th class="px-3 py-2">#</th>
                    <th class="px-3 py-2">Team</th>
                    <th class="px-3 py-2 text-right">Solved</th>
                    <th class="px-3 py-2 text-right">Points</th>
                    <th class="px-3 py-2 text-right">Hint cost</th>
                    <th class="px-3 py-2 text-right">Score</th>
                    <th class="px-3 py-2">Last solve</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ranking as $row)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2">{{ $loop->iteration }}</td>
                        <td class="px-3 py-2 font-medium">{{ $row->team->name }}</td>
                        <td class="px-3 py-2 text-right">{{ $row->solves }}</td>
                        <td class="px-3 py-2 text-right">{{ $row->points }}</td>
                        <td class="px-3 py-2 text-right">{{ $row->hint_cost }}</td>
                        <td class="px-3 py-2 text-right font-semibold">{{ $row->score }}</td>
                        <td class="px-3 py-2 text-slate-500">{{ $row->last_solved_at ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-slate-500">No team is registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
