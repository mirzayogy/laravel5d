@php
    $medals = ['bg-amber-300 text-amber-900', 'bg-slate-200 text-slate-700', 'bg-orange-200 text-orange-900'];
    $myTeamId = $myTeamId ?? null;
@endphp

<ol class="space-y-2">
    @forelse ($rows as $index => $row)
        <li class="flex items-center gap-3 rounded-2xl px-2 py-1.5 {{ $row->team->id === $myTeamId ? 'bg-coral-50 ring-2 ring-coral-200' : '' }}">
            <span class="flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-extrabold {{ $medals[$index] ?? 'bg-sky-50 text-sky-700' }}">{{ $index + 1 }}</span>
            <a href="{{ route('teams.show', $row->team) }}" class="min-w-0 grow truncate text-sm font-semibold hover:text-sky-700">{{ $row->team->name }}</a>
            <span class="text-xs text-slate-400">{{ $row->solves }} solved</span>
            <span class="w-14 text-right text-sm font-extrabold tabular-nums text-slate-800">{{ $row->score }}</span>
        </li>
    @empty
        <li class="py-4 text-center text-sm text-slate-500">No team is registered yet.</li>
    @endforelse
</ol>
