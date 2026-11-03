@extends('layouts.app')

@section('title', $writeup->title)

@section('content')
    <x-page-header :title="$writeup->title" icon="file-text" :crumbs="['Writeups' => route('writeups.index'), $writeup->title => null]">
        <x-slot:actions>
            @if ($writeup->status === 'draft')<x-badge tone="amber" class="text-sm">Draft, only you can see it</x-badge>@endif
            @if ($isAuthor)
                <x-button :href="route('writeups.edit', $writeup)" variant="ghost" size="sm" icon="edit">Edit</x-button>
                <form method="POST" action="{{ route('writeups.destroy', $writeup) }}" onsubmit="return confirm('Delete this writeup?')">
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <div class="md">{!! $html !!}</div>
        </x-card>

        <div class="space-y-6">
            <x-card>
                <div class="flex items-center gap-3">
                    <x-avatar :name="$writeup->user->name" size="lg" />
                    <div class="min-w-0">
                        <div class="truncate font-extrabold">{{ $writeup->user->name }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $writeup->user->profile?->institution ?? 'Flagboard player' }}</div>
                    </div>
                </div>
                @if ($writeup->user->profile?->bio)<p class="mt-3 text-sm text-slate-600">{{ $writeup->user->profile->bio }}</p>@endif
                <div class="mt-3 text-xs text-slate-400">Published {{ $writeup->created_at->diffForHumans() }}</div>
            </x-card>

            <x-card tone="sky">
                <h2 class="font-extrabold">About the challenge</h2>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <x-category-badge :name="$writeup->challenge->category->name" />
                    <x-difficulty :level="$writeup->challenge->difficulty" />
                </div>
                <div class="mt-2 font-bold">{{ $writeup->challenge->title }}</div>
                <div class="text-xs text-slate-500">{{ $writeup->challenge->competition->title }}, {{ $writeup->challenge->points }} points</div>
            </x-card>

            @if ($more->isNotEmpty())
                <x-card>
                    <h2 class="font-extrabold">More writeups for this challenge</h2>
                    <ul class="mt-2 divide-y divide-sky-50">
                        @foreach ($more as $other)
                            <li class="py-2 text-sm">
                                <a href="{{ route('writeups.show', $other) }}" class="font-semibold hover:text-sky-700">{{ $other->title }}</a>
                                <div class="text-xs text-slate-500">by {{ $other->user->name }}</div>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif
        </div>
    </div>
@endsection
