@extends('layouts.app')

@section('title', 'Writeups')

@section('content')
    <x-page-header title="Writeups" subtitle="Learn how other teams cracked the challenges, then share your own." icon="file-text">
        <x-slot:actions>
            @auth
                <x-button :href="route('writeups.index', ['mine' => 1])" :variant="$mine ? 'primary' : 'ghost'" icon="edit">My writeups ({{ $myCount }})</x-button>
            @endauth
        </x-slot:actions>
    </x-page-header>

    <x-card class="mb-6">
        <form method="GET" action="{{ route('writeups.index') }}" class="flex flex-wrap items-center gap-3">
            @if ($mine)<input type="hidden" name="mine" value="1">@endif
            <div class="relative min-w-56 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by title or challenge" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <select name="competition" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">All competitions</option>
                @foreach ($competitions as $competition)
                    <option value="{{ $competition->id }}" @selected((int) $competitionId === $competition->id)>{{ $competition->title }}</option>
                @endforeach
            </select>
            <x-button type="submit" icon="search">Filter</x-button>
            <span class="ml-auto text-xs font-semibold text-slate-500">{{ $mine ? $myCount.' of your writeups' : $total.' published' }}</span>
        </form>
    </x-card>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($writeups as $writeup)
            <x-card :hover="true" class="relative flex flex-col">
                <div class="flex flex-wrap items-center gap-1.5">
                    <x-category-badge :name="$writeup->challenge->category->name" />
                    @if ($writeup->status === 'draft')<x-badge tone="amber">Draft</x-badge>@endif
                </div>
                <a href="{{ route('writeups.show', $writeup) }}" class="mt-3 text-lg leading-tight font-extrabold tracking-tight after:absolute after:inset-0 after:rounded-3xl hover:text-sky-700">{{ $writeup->title }}</a>
                <p class="mt-1 line-clamp-3 text-sm text-slate-500">{{ $writeup->excerpt }}</p>
                <div class="mt-auto flex items-center gap-2 pt-4 text-xs text-slate-500">
                    <x-avatar :name="$writeup->user->name" size="sm" />
                    <div class="min-w-0">
                        <div class="truncate font-semibold text-slate-700">{{ $writeup->user->name }}</div>
                        <div class="truncate">{{ $writeup->challenge->title }}, {{ $writeup->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                <x-empty icon="file-text" title="No writeup found" text="Solve a challenge, then open it to write about how you did it.">
                    <x-button :href="route('competitions.index')" variant="accent" icon="flag">See competitions</x-button>
                </x-empty>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $writeups->links('pagination.custom') }}</div>
@endsection
