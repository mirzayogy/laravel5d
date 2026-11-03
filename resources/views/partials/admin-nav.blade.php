@php
    $items = [
        ['Overview', 'admin.dashboard', 'grid', ['admin.dashboard']],
        ['Competitions', 'admin.competitions.index', 'flag', ['admin.competitions.*']],
        ['Challenges', 'admin.challenges.index', 'target', ['admin.challenges.*']],
        ['Users', 'admin.users.index', 'user', ['admin.users.*']],
        ['Teams', 'admin.teams.index', 'users', ['admin.teams.*']],
        ['Categories', 'admin.categories.index', 'layers', ['admin.categories.*']],
        ['Tags', 'admin.tags.index', 'tag', ['admin.tags.*']],
    ];
@endphp

<nav class="mb-6 flex flex-wrap gap-1.5 rounded-3xl border-2 border-sky-100 bg-white p-1.5" aria-label="Admin">
    @foreach ($items as [$label, $route, $icon, $patterns])
        <a href="{{ route($route) }}" @if (request()->routeIs(...$patterns)) aria-current="page" @endif
           class="flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-sm font-semibold transition {{ request()->routeIs(...$patterns) ? 'bg-sky-500 text-white shadow-[0_2px_0_0_var(--color-sky-700)]' : 'text-slate-600 hover:bg-sky-50' }}">
            <x-icon :name="$icon" class="size-4" /> {{ $label }}
        </a>
    @endforeach
</nav>
