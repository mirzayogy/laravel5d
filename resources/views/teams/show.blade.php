@extends('layouts.app')

@section('title', 'My team')

@section('content')
    @if ($team)
        <h1 class="text-2xl font-semibold">{{ $team->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">
            Invite code: <span class="rounded bg-slate-200 px-2 py-0.5 font-mono">{{ $team->invite_code }}</span>
        </p>

        <h2 class="mt-6 text-lg font-semibold">Members</h2>
        <ul class="mt-2 list-inside list-disc text-sm">
            @foreach ($team->users as $member)
                <li>{{ $member->name }}</li>
            @endforeach
        </ul>

        <h2 class="mt-6 text-lg font-semibold">Registered competitions</h2>
        <ul class="mt-2 space-y-1 text-sm">
            @forelse ($team->competitions as $competition)
                <li>
                    <a href="{{ route('competitions.show', $competition) }}" class="text-indigo-700 underline">{{ $competition->title }}</a>
                    <span class="text-slate-400">registered {{ \Illuminate\Support\Carbon::parse($competition->pivot->registered_at)->format('d M Y H:i') }}</span>
                </li>
            @empty
                <li class="text-slate-500">Not registered in any competition yet.</li>
            @endforelse
        </ul>

        <form method="POST" action="{{ route('team.leave') }}" class="mt-6">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded border border-red-300 px-3 py-1 text-sm text-red-700 hover:bg-red-50">Leave team</button>
        </form>
    @else
        <h1 class="text-2xl font-semibold">My team</h1>
        <p class="mt-1 text-slate-600">You are not in a team yet. Create one or join with an invite code.</p>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <form method="POST" action="{{ route('team.store') }}" class="rounded border border-slate-200 bg-white p-4">
                @csrf
                <h2 class="font-semibold">Create a team</h2>
                <label for="name" class="mt-3 block text-sm font-medium">Team name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2">
                <button type="submit" class="mt-3 rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Create team</button>
            </form>

            <form method="POST" action="{{ route('team.join') }}" class="rounded border border-slate-200 bg-white p-4">
                @csrf
                <h2 class="font-semibold">Join a team</h2>
                <label for="invite_code" class="mt-3 block text-sm font-medium">Invite code</label>
                <input id="invite_code" name="invite_code" type="text" value="{{ old('invite_code') }}" required
                       class="mt-1 w-full rounded border border-slate-300 px-3 py-2 font-mono uppercase">
                <button type="submit" class="mt-3 rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-500">Join team</button>
            </form>
        </div>
    @endif
@endsection
