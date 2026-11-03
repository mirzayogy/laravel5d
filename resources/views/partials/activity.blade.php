@php
    $styles = [
        'solve' => ['check-circle', 'bg-emerald-100 text-emerald-700'],
        'first' => ['zap', 'bg-coral-100 text-coral-700'],
        'hint' => ['bulb', 'bg-amber-100 text-amber-700'],
        'registration' => ['flag', 'bg-sky-100 text-sky-700'],
        'writeup' => ['file-text', 'bg-violet-100 text-violet-700'],
    ];
@endphp

<ul class="divide-y divide-sky-50">
    @forelse ($events as $event)
        @php
            $key = $event['type'] === 'solve' && ($event['first_blood'] ?? false) ? 'first' : $event['type'];
            [$icon, $tone] = $styles[$key];
        @endphp
        <li class="flex items-start gap-3 py-3">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-full {{ $tone }}"><x-icon :name="$icon" class="size-4" /></span>
            <div class="min-w-0 grow text-sm leading-snug">
                @if ($event['type'] === 'writeup')
                    <span class="font-semibold">{{ $event['author'] }}</span> wrote
                    <a href="{{ route('writeups.show', $event['writeup_id']) }}" class="font-semibold text-sky-700 hover:underline">{{ $event['writeup'] }}</a>
                @else
                    <a href="{{ route('teams.show', $event['team_id']) }}" class="font-semibold hover:text-sky-700">{{ $event['team'] }}</a>
                    @if ($event['type'] === 'solve')
                        solved
                        @auth<a href="{{ route('challenges.show', $event['challenge_id']) }}" class="font-semibold text-sky-700 hover:underline">{{ $event['title'] }}</a>@else<span class="font-semibold">{{ $event['title'] }}</span>@endauth
                        <x-badge tone="green" class="ml-1">+{{ $event['points'] }}</x-badge>
                        @if ($event['first_blood'])<x-badge tone="coral" class="ml-1"><x-icon name="zap" class="size-3" /> First blood</x-badge>@endif
                    @elseif ($event['type'] === 'hint')
                        unlocked a hint for <span class="font-semibold">{{ $event['title'] }}</span>
                        <x-badge tone="amber" class="ml-1">-{{ $event['cost'] }}</x-badge>
                    @else
                        registered for
                        <a href="{{ route('competitions.show', $event['competition_id']) }}" class="font-semibold text-sky-700 hover:underline">{{ $event['title'] }}</a>
                    @endif
                @endif
                <div class="text-xs text-slate-400">{{ $event['at']->diffForHumans() }}</div>
            </div>
        </li>
    @empty
        <li class="py-6 text-center text-sm text-slate-500">Nothing has happened yet. The first solve will show up here.</li>
    @endforelse
</ul>
