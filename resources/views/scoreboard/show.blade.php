@extends('layouts.app')

@section('title', 'Scoreboard')

@section('content')
    @php($medals = [['bg-amber-300 text-amber-900', 'ring-amber-300'], ['bg-slate-200 text-slate-700', 'ring-slate-300'], ['bg-orange-200 text-orange-900', 'ring-orange-300']])

    <x-page-header title="Scoreboard" :subtitle="$competition->title" icon="trophy"
                   :crumbs="['Competitions' => route('competitions.index'), $competition->title => route('competitions.show', $competition), 'Scoreboard' => null]">
        <x-slot:actions>
            <x-phase-badge :phase="$competition->phase" class="text-sm" />
            @if ($competition->isRunning())<x-countdown :target="$competition->end_at" />@endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat icon="users" tone="sky" label="Teams" :value="$stats['teams']" />
        <x-stat icon="check-circle" tone="green" label="Solves" :value="$stats['solves']" />
        <x-stat icon="target" tone="amber" label="Attempts" :value="$stats['attempts']" />
        <x-stat icon="flag" tone="coral" label="Challenges" :value="$stats['challenges']" :hint="number_format($stats['points']).' points'" />
    </div>

    @if ($ranking->isEmpty())
        <x-empty class="mt-6" icon="trophy" title="No team yet" text="The scoreboard fills up as soon as teams register." />
    @else
        <div x-data="scoreboard({ url: '{{ route('scoreboard.data', $competition) }}', live: {{ $competition->isRunning() ? 'true' : 'false' }}, myTeamId: {{ $myTeamId ?? 'null' }}, series: @js($series) })">
            {{-- Podium --}}
            <div class="mt-6 grid items-end gap-4 sm:grid-cols-3">
                @foreach ([1, 0, 2] as $slot)
                    @if (isset($ranking[$slot]))
                        @php($row = $ranking[$slot])
                        <x-card class="{{ $slot === 0 ? 'sm:order-2 sm:pb-10' : ($slot === 1 ? 'sm:order-1' : 'sm:order-3') }} text-center ring-4 {{ $medals[$slot][1] }} ring-offset-2 ring-offset-white">
                            <span class="mx-auto flex size-12 items-center justify-center rounded-full text-lg font-extrabold {{ $medals[$slot][0] }}">
                                @if ($slot === 0)<x-icon name="crown" class="size-6" />@else{{ $slot + 1 }}@endif
                            </span>
                            <a href="{{ route('teams.show', $row->team) }}" class="mt-2 block truncate text-lg font-extrabold hover:text-sky-700">{{ $row->team->name }}</a>
                            <div class="text-3xl font-extrabold tracking-tight text-coral-600 tabular-nums">{{ $row->score }}</div>
                            <div class="text-xs font-semibold text-slate-500">{{ $row->solves }} solved, {{ $row->hint_cost }} hint cost</div>
                        </x-card>
                    @endif
                @endforeach
            </div>

            <x-card class="mt-6">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight"><x-icon name="trending-up" class="size-5 text-sky-600" /> Score over time</h2>
                    <span class="text-xs font-semibold text-slate-500">@if ($competition->isRunning())<span class="mr-1 inline-block size-2 animate-pulse rounded-full bg-emerald-500"></span>Live, refreshes every 20 seconds @else Final standings @endif <span x-show="updatedAt" x-cloak>(updated <span x-text="updatedAt"></span>)</span></span>
                </div>
                <div class="h-72"><canvas x-ref="chart"></canvas></div>
            </x-card>

            <x-card :padded="false" class="mt-6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-sky-50 text-left text-xs font-bold tracking-wide text-slate-500 uppercase">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Team</th>
                                <th class="px-4 py-3 text-right">Solved</th>
                                <th class="px-4 py-3 text-right">Points</th>
                                <th class="px-4 py-3 text-right">Hint cost</th>
                                <th class="px-4 py-3 text-right">Score</th>
                                <th class="px-4 py-3">Last solve</th>
                            </tr>
                        </thead>
                        <tbody x-ref="body">
                            @foreach ($ranking as $index => $row)
                                <tr class="border-t border-sky-50 {{ $row->team->id === $myTeamId ? 'bg-coral-50 font-medium' : 'hover:bg-sky-50/60' }}">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">{{ $row->team->name }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ $row->solves }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ $row->points }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ $row->hint_cost }}</td>
                                    <td class="px-4 py-3 text-right font-extrabold tabular-nums">{{ $row->score }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $row->last_solved_at ? \Illuminate\Support\Carbon::parse($row->last_solved_at)->format('d M H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    @endif
@endsection
