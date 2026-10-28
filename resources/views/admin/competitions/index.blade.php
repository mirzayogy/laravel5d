@extends('layouts.app')

@section('title', 'Manage competitions')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Manage competitions</h1>
        <a href="{{ route('admin.competitions.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">New competition</a>
    </div>

    <div class="mt-4 overflow-x-auto rounded border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-slate-600">
                <tr>
                    <th class="px-3 py-2">Title</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Period</th>
                    <th class="px-3 py-2 text-right">Challenges</th>
                    <th class="px-3 py-2 text-right">Teams</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($competitions as $competition)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 font-medium">{{ $competition->title }}</td>
                        <td class="px-3 py-2">{{ $competition->status }}</td>
                        <td class="px-3 py-2 text-slate-500">{{ $competition->start_at->format('d M Y H:i') }} to {{ $competition->end_at->format('d M Y H:i') }}</td>
                        <td class="px-3 py-2 text-right">{{ $competition->challenges_count }}</td>
                        <td class="px-3 py-2 text-right">{{ $competition->teams_count }}</td>
                        <td class="flex justify-end gap-2 px-3 py-2">
                            <a href="{{ route('admin.competitions.edit', $competition) }}" class="rounded border border-slate-300 px-2 py-1 hover:bg-slate-100">Edit</a>
                            <form method="POST" action="{{ route('admin.competitions.destroy', $competition) }}"
                                  onsubmit="return confirm('Delete this competition and all of its challenges?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded border border-red-300 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">No competition yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
