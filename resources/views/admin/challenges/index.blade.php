@extends('layouts.app')

@section('title', 'Manage challenges')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Manage challenges</h1>
        <a href="{{ route('admin.challenges.create') }}" class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">New challenge</a>
    </div>

    <div class="mt-4 overflow-x-auto rounded border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-slate-600">
                <tr>
                    <th class="px-3 py-2">Title</th>
                    <th class="px-3 py-2">Competition</th>
                    <th class="px-3 py-2">Category</th>
                    <th class="px-3 py-2 text-right">Points</th>
                    <th class="px-3 py-2">Published</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($challenges as $challenge)
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 font-medium">{{ $challenge->title }}</td>
                        <td class="px-3 py-2">{{ $challenge->competition->title }}</td>
                        <td class="px-3 py-2">{{ $challenge->category->name }}</td>
                        <td class="px-3 py-2 text-right">{{ $challenge->points }}</td>
                        <td class="px-3 py-2">{{ $challenge->is_published ? 'Yes' : 'No' }}</td>
                        <td class="flex justify-end gap-2 px-3 py-2">
                            <a href="{{ route('admin.challenges.edit', $challenge) }}" class="rounded border border-slate-300 px-2 py-1 hover:bg-slate-100">Edit</a>
                            <form method="POST" action="{{ route('admin.challenges.destroy', $challenge) }}"
                                  onsubmit="return confirm('Delete this challenge with its hints and submissions?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded border border-red-300 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">No challenge yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $challenges->links() }}</div>
@endsection
