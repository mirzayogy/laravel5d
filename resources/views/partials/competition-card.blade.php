@php
    $registered = $registered ?? false;
    $total = max(1, $competition->start_at->diffInSeconds($competition->end_at));
    $elapsed = $competition->isFinished() ? 100 : ($competition->isUpcoming() ? 0 : min(100, max(0, $competition->start_at->diffInSeconds(now()) / $total * 100)));
@endphp

<x-card hover class="flex h-full flex-col">
    <div class="flex items-start justify-between gap-3">
        <x-phase-badge :phase="$competition->phase" />
        @if ($registered)
            <x-badge tone="green"><x-icon name="check" class="size-3" /> Registered</x-badge>
        @endif
    </div>

    <a href="{{ route('competitions.show', $competition) }}" class="mt-3 text-lg leading-tight font-extrabold tracking-tight text-slate-900 hover:text-sky-700">{{ $competition->title }}</a>
    <p class="mt-1 line-clamp-2 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($competition->description), 120) }}</p>

    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs font-medium text-slate-600">
        <span class="flex items-center gap-1"><x-icon name="calendar" class="size-3.5 text-sky-500" /> {{ $competition->start_at->format('d M') }} to {{ $competition->end_at->format('d M Y') }}</span>
        <span class="flex items-center gap-1"><x-icon name="flag" class="size-3.5 text-coral-500" /> {{ $competition->challenges_count }} challenges</span>
        <span class="flex items-center gap-1"><x-icon name="users" class="size-3.5 text-emerald-500" /> {{ $competition->teams_count }} teams</span>
    </div>

    <div class="mt-4">
        <x-progress :value="$elapsed" :tone="$competition->isRunning() ? 'coral' : 'sky'" />
        <div class="mt-3 flex min-h-12 items-center justify-between gap-2">
            @if ($competition->isRunning())
                <span class="text-xs font-semibold text-slate-500">Ends in</span>
                <x-countdown :target="$competition->end_at" />
            @elseif ($competition->isUpcoming())
                <span class="text-xs font-semibold text-slate-500">Starts in</span>
                <x-countdown :target="$competition->start_at" />
            @else
                <span class="text-xs font-semibold text-slate-500">Ended {{ $competition->end_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    <div class="mt-auto flex flex-wrap gap-2 pt-4">
        <x-button :href="route('competitions.show', $competition)" size="sm" :variant="$competition->isRunning() ? 'accent' : 'primary'" icon="arrow-right">Details</x-button>
        @unless ($competition->isUpcoming())
            <x-button :href="route('scoreboard.show', $competition)" size="sm" variant="ghost" icon="trophy">Scoreboard</x-button>
        @endunless
    </div>
</x-card>
