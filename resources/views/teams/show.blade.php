@extends('layouts.app')

@section('title', 'My team')

@section('content')
    @if (! $team)
        <x-page-header title="Find your team" subtitle="Play together. Create a team or join friends with an invite code." icon="users" />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="grid gap-6 sm:grid-cols-2 lg:col-span-2">
                <x-card tone="sky" class="flex flex-col">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-sky-500 text-white shadow-[0_3px_0_0_var(--color-sky-700)]"><x-icon name="plus" class="size-6" /></span>
                    <h2 class="mt-3 text-lg font-extrabold">Create a team</h2>
                    <p class="text-sm text-slate-600">You become the captain and get an invite code to share.</p>
                    <form method="POST" action="{{ route('team.store') }}" class="mt-4 space-y-3">
                        @csrf
                        <x-input name="name" label="Team name" required maxlength="50" placeholder="Null Pointers" />
                        <x-button type="submit" class="w-full" icon="user-plus">Create team</x-button>
                    </form>
                </x-card>

                <x-card tone="coral" class="flex flex-col">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-coral-500 text-white shadow-[0_3px_0_0_var(--color-coral-700)]"><x-icon name="key" class="size-6" /></span>
                    <h2 class="mt-3 text-lg font-extrabold">Join with a code</h2>
                    <p class="text-sm text-slate-600">Ask the captain for the 8-character invite code. Teams hold up to {{ $size }} players.</p>
                    <form method="POST" action="{{ route('team.join') }}" class="mt-4 space-y-3">
                        @csrf
                        <x-input name="invite_code" label="Invite code" required maxlength="20" placeholder="ABCD1234" class="font-mono uppercase" />
                        <x-button type="submit" variant="accent" class="w-full" icon="arrow-right">Join team</x-button>
                    </form>
                </x-card>

                <x-card class="sm:col-span-2">
                    <h2 class="text-lg font-extrabold">Why play in a team?</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                        @foreach ([['users', 'Share the work', 'Split the categories and solve more.'], ['bulb', 'Learn faster', 'Teammates explain what you missed.'], ['trophy', 'Win together', 'One score, one scoreboard spot.']] as [$icon, $title, $text])
                            <div class="rounded-2xl bg-sky-50 p-3"><x-icon :name="$icon" class="size-5 text-sky-600" /><div class="mt-1 text-sm font-bold">{{ $title }}</div><div class="text-xs text-slate-600">{{ $text }}</div></div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <x-card class="self-start">
                <div class="flex items-center justify-between">
                    <h2 class="font-extrabold">Top teams</h2>
                    <a href="{{ route('teams.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">All teams</a>
                </div>
                <ul class="mt-3 space-y-2">
                    @foreach ($topTeams as $top)
                        <li class="flex items-center gap-3">
                            <x-avatar :name="$top->name" size="sm" />
                            <a href="{{ route('teams.show', $top) }}" class="min-w-0 grow truncate text-sm font-semibold hover:text-sky-700">{{ $top->name }}</a>
                            <span class="text-xs text-slate-500">{{ $top->users_count }} members</span>
                            <span class="text-sm font-extrabold tabular-nums">{{ $top->solves_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-card>
        </div>
    @else
        <x-page-header :title="$team->name" subtitle="Your team space. Manage members, share the invite code, and follow your standings." icon="users">
            <x-slot:actions>
                <x-button :href="route('teams.show', $team)" variant="ghost" icon="eye">Public page</x-button>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat icon="check-circle" tone="green" label="Solves" :value="$solves" />
            <x-stat icon="users" tone="sky" label="Members" :value="$team->users->count().' / '.$size" />
            <x-stat icon="flag" tone="coral" label="Competitions" :value="$team->competitions->count()" />
            <x-stat icon="crown" tone="amber" label="Captain" :value="\Illuminate\Support\Str::before($team->captain?->name ?? 'none', ' ')" :hint="$isCaptain ? 'That is you' : null" />
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <x-card>
                    <h2 class="mb-3 text-lg font-extrabold tracking-tight">Members</h2>
                    <ul class="divide-y divide-sky-50">
                        @foreach ($team->users as $member)
                            <li class="flex flex-wrap items-center gap-3 py-3">
                                <x-avatar :name="$member->name" />
                                <div class="min-w-0 grow">
                                    <div class="flex items-center gap-1.5 text-sm font-bold">
                                        <span class="truncate">{{ $member->name }}</span>
                                        @if ($team->isCaptain($member))<x-badge tone="amber"><x-icon name="crown" class="size-3" /> Captain</x-badge>@endif
                                        @if ($member->id === auth()->id())<x-badge tone="sky">You</x-badge>@endif
                                    </div>
                                    <div class="truncate text-xs text-slate-500">{{ $member->profile?->institution ?? $member->email }}</div>
                                </div>
                                @if ($isCaptain && $member->id !== auth()->id())
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('team.captain', $member) }}" onsubmit="return confirm('Make {{ $member->name }} the captain?')">
                                            @csrf
                                            <x-button type="submit" size="sm" variant="ghost" icon="crown">Make captain</x-button>
                                        </form>
                                        <form method="POST" action="{{ route('team.members.remove', $member) }}" onsubmit="return confirm('Remove {{ $member->name }} from the team?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" size="sm" variant="danger" icon="user-minus">Remove</x-button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-card>

                <x-card>
                    <h2 class="mb-3 text-lg font-extrabold tracking-tight">Competitions</h2>
                    <div class="space-y-3">
                        @forelse ($standings as $row)
                            <div class="flex flex-wrap items-center gap-3 rounded-2xl border-2 border-sky-100 p-3">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-extrabold text-sky-800">{{ $row['standing'] && ! $row['competition']->isUpcoming() ? '#'.$row['standing']['rank'] : '-' }}</span>
                                <div class="min-w-0 grow">
                                    <a href="{{ route('competitions.show', $row['competition']) }}" class="block truncate font-bold hover:text-sky-700">{{ $row['competition']->title }}</a>
                                    <span class="text-xs text-slate-500">{{ $row['standing']['row']->solves ?? 0 }} solved, {{ $row['standing']['row']->score ?? 0 }} points</span>
                                </div>
                                <x-phase-badge :phase="$row['competition']->phase" />
                                @if ($row['competition']->isRunning())
                                    <x-button :href="route('challenges.index', $row['competition'])" size="sm" variant="accent" icon="target">Play</x-button>
                                @endif
                            </div>
                        @empty
                            <x-empty icon="flag" title="Not registered yet" text="Register your team for a competition to start scoring.">
                                <x-button :href="route('competitions.index')" variant="accent" icon="flag">See competitions</x-button>
                            </x-empty>
                        @endforelse
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card tone="sky">
                    <h2 class="font-extrabold">Invite code</h2>
                    <div x-data="copy('{{ $team->invite_code }}')" class="mt-3 flex items-center gap-2">
                        <code class="grow rounded-2xl bg-white px-4 py-2.5 text-center font-mono text-xl font-extrabold tracking-widest text-sky-800 ring-2 ring-sky-200">{{ $team->invite_code }}</code>
                        <button type="button" @click="copy()" class="flex size-11 items-center justify-center rounded-full bg-sky-500 text-white shadow-[0_3px_0_0_var(--color-sky-700)] hover:bg-sky-400" aria-label="Copy the invite code">
                            <x-icon name="copy" x-show="! copied" class="size-5" />
                            <x-icon name="check" x-show="copied" x-cloak class="size-5" />
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-slate-600">Share this code with your friends. The team holds up to {{ $size }} players.</p>
                    @if ($isCaptain)
                        <form method="POST" action="{{ route('team.code') }}" class="mt-3" onsubmit="return confirm('Create a new code? The old one stops working.')">
                            @csrf
                            <x-button type="submit" size="sm" variant="ghost" icon="refresh">New code</x-button>
                        </form>
                    @endif
                </x-card>

                @if ($isCaptain)
                    <x-card>
                        <h2 class="font-extrabold">Team settings</h2>
                        <form method="POST" action="{{ route('team.update') }}" class="mt-3 space-y-3">
                            @csrf
                            @method('PUT')
                            <x-input name="name" label="Team name" :value="$team->name" required maxlength="50" />
                            <x-button type="submit" size="sm" icon="check">Rename</x-button>
                        </form>
                        <form method="POST" action="{{ route('team.destroy') }}" class="mt-4 border-t border-sky-100 pt-4" onsubmit="return confirm('Disband the team? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <p class="mb-2 text-xs text-slate-500">Disbanding is only possible while the team has no submissions.</p>
                            <x-button type="submit" size="sm" variant="danger" icon="trash">Disband team</x-button>
                        </form>
                    </x-card>
                @endif

                <x-card>
                    <h2 class="font-extrabold">Leave team</h2>
                    <p class="mt-1 text-xs text-slate-500">@if ($isCaptain && $team->users->count() > 1)The next member becomes the captain.@else You can join or create another team afterwards.@endif</p>
                    <form method="POST" action="{{ route('team.leave') }}" class="mt-3" onsubmit="return confirm('Leave this team?')">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" size="sm" variant="danger" icon="log-out">Leave team</x-button>
                    </form>
                </x-card>
            </div>
        </div>
    @endif
@endsection
