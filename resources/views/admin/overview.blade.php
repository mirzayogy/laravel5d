@php $max = max(1, $signups->max('count')); @endphp

<x-app-layout title="{{ __('Overview') }}" :crumbs="[[__('Admin')], [__('Overview')]]">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">{{ __('Admin console') }}</h1>
            <p class="text-body-sm text-pewter">{{ __('The whole system at a glance. Habits, moods and journals stay private to their owners.') }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary"><x-icon name="users" /> {{ __('Manage users') }}</a>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
        @foreach ($stats as $label => $value)
            <div class="card p-5">
                <p class="text-caption text-pewter">{{ __($label) }}</p>
                <p class="mt-2 text-[32px] font-semibold leading-none tracking-tight"><x-counter :value="$value" /></p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2" aria-labelledby="signups-title">
            <div class="card-header">
                <div><h2 id="signups-title" class="text-subheading">{{ __('New users') }}</h2><p class="text-body-sm text-pewter">{{ __('Last 14 days · :count :role', ['count' => $admins, 'role' => __($admins === 1 ? 'administrator' : 'administrators')]) }}</p></div>
            </div>
            <div class="card-body">
                <div class="flex h-36 items-end gap-1.5" role="img" aria-label="{{ __('Sign-ups per day for the last 14 days') }}">
                    @foreach ($signups as $day)
                        <div class="group/bar flex h-full flex-1 flex-col items-center justify-end gap-1.5" title="{{ $day['date']->translatedFormat('j M') }}: {{ $day['count'] }}">
                            <span class="text-caption text-pewter">{{ $day['count'] ?: '' }}</span>
                            <span class="w-full rounded-md {{ $day['count'] ? 'bg-ember' : 'bg-fog' }}" style="height: {{ $day['count'] ? max(8, $day['count'] / $max * 100) : 4 }}%"></span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 flex justify-between text-caption text-pewter"><span>{{ $signups->first()['date']->translatedFormat('j M') }}</span><span>{{ $signups->last()['date']->translatedFormat('j M') }}</span></div>
            </div>
        </section>

        <section class="card" aria-labelledby="top-title">
            <div class="card-header"><h2 id="top-title" class="text-subheading">{{ __('Top categories') }}</h2><span class="text-caption text-pewter">{{ __('all users') }}</span></div>
            <ul class="card-body space-y-3">
                @forelse ($topCategories as $c)
                    <li>
                        <div class="mb-1 flex items-center justify-between text-body-sm">
                            <span class="flex items-center gap-2"><span class="size-3 rounded-md" style="background: {{ $c->color }}"></span>{{ $c->icon }} {{ $c->display_name }}</span>
                            <span class="font-semibold">{{ $c->completed }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded bg-fog"><span class="block h-full rounded" style="width: {{ $c->completed / $topCategories->max('completed') * 100 }}%; background: {{ $c->color }}"></span></div>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">{{ __('No completed check-ins yet.') }}</li>
                @endforelse
            </ul>
        </section>

        <section class="card lg:col-span-2" aria-labelledby="recent-title">
            <div class="card-header"><h2 id="recent-title" class="text-subheading">{{ __('Newest users') }}</h2><a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">{{ __('All users') }}</a></div>
            <ul class="divide-y divide-mist/40">
                @foreach ($recentUsers as $u)
                    <li class="flex items-center gap-3 px-6 py-3">
                        <x-avatar :user="$u" size="size-9" />
                        <span class="min-w-0 flex-1"><span class="block truncate text-body-sm font-semibold">{{ $u->name }}</span><span class="block truncate text-caption text-pewter">{{ $u->email }}</span></span>
                        @if ($u->isAdmin()) <span class="badge badge-active">{{ __('Admin') }}</span> @endif
                        <span class="text-caption text-pewter">{{ $u->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="card" aria-labelledby="sys-title">
            <div class="card-header"><h2 id="sys-title" class="text-subheading">{{ __('System') }}</h2><x-icon name="server" class="size-4 text-steel" /></div>
            <dl class="card-body space-y-2 text-body-sm">
                @foreach ($system as $k => $v)
                    <div class="flex justify-between gap-3"><dt class="text-pewter">{{ __($k) }}</dt><dd class="font-medium">{{ $v }}</dd></div>
                @endforeach
            </dl>
        </section>
    </div>
</x-app-layout>
