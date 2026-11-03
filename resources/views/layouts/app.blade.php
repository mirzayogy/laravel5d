<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Flagboard') - Flagboard</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='9' fill='%23ff6b4a'/%3E%3Cpath d='M10 24V8m0 0h12l-3 4.5 3 4.5H10' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="flex min-h-screen flex-col bg-canvas text-slate-800 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-24 -left-24 size-96 rounded-full bg-sky-200/50 blur-3xl"></div>
        <div class="absolute top-1/3 -right-32 size-96 rounded-full bg-coral-200/40 blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/4 size-80 rounded-full bg-sky-100/80 blur-3xl"></div>
    </div>

    @php
        $user = auth()->user();
        $links = [
            ['Home', 'home', 'home', ['home']],
            ['Competitions', 'competitions.index', 'flag', ['competitions.*', 'challenges.*', 'scoreboard.*']],
            ['Writeups', 'writeups.index', 'file-text', ['writeups.*']],
        ];
        if ($user) {
            $links[] = ['Dashboard', 'dashboard', 'grid', ['dashboard']];
            $links[] = ['My team', 'team.show', 'users', ['team.*', 'teams.*']];
        }
        if ($user?->isAdmin()) {
            $links[] = ['Admin', 'admin.dashboard', 'shield', ['admin.*']];
        }
        $unread = $user ? $user->unreadNotifications()->count() : 0;
    @endphp

    <header class="sticky top-0 z-40 border-b-2 border-sky-100 bg-white/85 backdrop-blur" x-data="{ menu: false }">
        <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-slate-900">
                <span class="flex size-9 items-center justify-center rounded-xl bg-coral-500 text-white shadow-[0_3px_0_0_var(--color-coral-700)]">
                    <x-icon name="flag" class="size-5" />
                </span>
                Flagboard
            </a>

            <nav class="ml-4 hidden items-center gap-1 lg:flex" aria-label="Main">
                @foreach ($links as [$label, $route, $icon, $patterns])
                    @php($active = request()->routeIs(...$patterns))
                    <a href="{{ route($route) }}" @if ($active) aria-current="page" @endif
                       class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold transition {{ $active ? 'bg-sky-100 text-sky-800' : 'text-slate-600 hover:bg-sky-50 hover:text-sky-800' }}">
                        <x-icon :name="$icon" class="size-4" />
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-2">
                @auth
                    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" @click="open = ! open" class="relative flex size-10 items-center justify-center rounded-full border-2 border-sky-100 bg-white text-slate-600 hover:border-sky-300" aria-label="Notifications">
                            <x-icon name="bell" class="size-5" />
                            @if ($unread > 0)
                                <span class="absolute -top-1 -right-1 flex min-w-5 items-center justify-center rounded-full bg-coral-500 px-1 text-[11px] font-bold text-white ring-2 ring-white">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </button>
                        <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                             class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-3xl border-2 border-sky-100 bg-white shadow-[0_6px_0_0_var(--color-sky-100)]">
                            <div class="flex items-center justify-between border-b border-sky-100 px-4 py-3">
                                <span class="text-sm font-bold">Notifications</span>
                                @if ($unread > 0)
                                    <form method="POST" action="{{ route('notifications.read-all') }}">
                                        @csrf
                                        <button class="text-xs font-semibold text-sky-700 hover:underline">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            @forelse ($user->notifications()->latest()->limit(5)->get() as $notification)
                                <a href="{{ $notification->data['url'] ?? route('notifications.index') }}" class="flex gap-3 px-4 py-3 text-sm hover:bg-sky-50 {{ $notification->read_at ? '' : 'bg-sky-50/70' }}">
                                    <x-icon :name="$notification->data['icon'] ?? 'bell'" class="mt-0.5 size-4 text-sky-600" />
                                    <span class="min-w-0">
                                        <span class="block truncate font-semibold">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                        <span class="block truncate text-xs text-slate-500">{{ $notification->data['body'] ?? '' }}</span>
                                    </span>
                                </a>
                            @empty
                                <p class="px-4 py-6 text-center text-sm text-slate-500">You are all caught up.</p>
                            @endforelse
                            <a href="{{ route('notifications.index') }}" class="block border-t border-sky-100 px-4 py-2.5 text-center text-sm font-semibold text-sky-700 hover:bg-sky-50">See all</a>
                        </div>
                    </div>

                    <div class="relative hidden sm:block" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" @click="open = ! open" class="flex items-center gap-2 rounded-full border-2 border-sky-100 bg-white py-1 pr-3 pl-1 hover:border-sky-300">
                            <x-avatar :name="$user->name" size="sm" />
                            <span class="max-w-28 truncate text-sm font-semibold">{{ $user->name }}</span>
                            <x-icon name="chevron-down" class="size-4 text-slate-400" />
                        </button>
                        <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                             class="absolute right-0 mt-2 w-56 overflow-hidden rounded-3xl border-2 border-sky-100 bg-white py-1 shadow-[0_6px_0_0_var(--color-sky-100)]">
                            <div class="border-b border-sky-100 px-4 py-2.5">
                                <div class="truncate text-sm font-bold">{{ $user->name }}</div>
                                <div class="truncate text-xs text-slate-500">{{ $user->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-sky-50"><x-icon name="user" class="size-4" /> Profile</a>
                            <a href="{{ route('team.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-sky-50"><x-icon name="users" class="size-4" /> My team</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-coral-700 hover:bg-coral-50"><x-icon name="log-out" class="size-4" /> Log out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <x-button :href="route('login')" variant="ghost" size="sm" class="hidden sm:inline-flex">Log in</x-button>
                    <x-button :href="route('register')" variant="accent" size="sm">Join now</x-button>
                @endauth

                <button type="button" class="flex size-10 items-center justify-center rounded-full border-2 border-sky-100 bg-white text-slate-600 lg:hidden" @click="menu = ! menu" aria-label="Menu">
                    <x-icon name="menu" class="size-5" />
                </button>
            </div>
        </div>

        <div x-cloak x-show="menu" x-transition class="border-t border-sky-100 bg-white px-4 py-3 lg:hidden">
            <nav class="grid gap-1" aria-label="Mobile">
                @foreach ($links as [$label, $route, $icon, $patterns])
                    <a href="{{ route($route) }}" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold {{ request()->routeIs(...$patterns) ? 'bg-sky-100 text-sky-800' : 'text-slate-600 hover:bg-sky-50' }}">
                        <x-icon :name="$icon" class="size-4" /> {{ $label }}
                    </a>
                @endforeach
                @auth
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-sky-50"><x-icon name="user" class="size-4" /> Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex w-full items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold text-coral-700 hover:bg-coral-50"><x-icon name="log-out" class="size-4" /> Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-sky-50"><x-icon name="key" class="size-4" /> Log in</a>
                @endauth
            </nav>
        </div>
    </header>

    @if ($user && ! $user->hasVerifiedEmail())
        <div class="border-b-2 border-amber-200 bg-amber-50">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-3 px-4 py-2.5 text-sm text-amber-900">
                <x-icon name="mail" class="size-4" />
                <span class="font-medium">Verify your email to register teams, submit flags, and write writeups.</span>
                <a href="{{ route('verification.notice') }}" class="ml-auto font-bold underline">Verify now</a>
            </div>
        </div>
    @endif

    <div class="pointer-events-none fixed top-20 right-4 z-50 grid w-80 max-w-[calc(100vw-2rem)] gap-2">
        @foreach (['success' => ['green', 'check-circle'], 'error' => ['coral', 'alert'], 'status' => ['sky', 'info']] as $key => [$tone, $icon])
            @if (session($key))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6500)" x-show="show" x-transition
                     class="pointer-events-auto flex items-start gap-3 rounded-2xl border-2 px-4 py-3 text-sm font-medium shadow-lg
                     {{ ['green' => 'border-emerald-200 bg-emerald-50 text-emerald-900', 'coral' => 'border-coral-200 bg-coral-50 text-coral-900', 'sky' => 'border-sky-200 bg-sky-50 text-sky-900'][$tone] }}" role="status">
                    <x-icon :name="$icon" class="mt-0.5 size-5" />
                    <span class="grow">{{ session($key) }}</span>
                    <button type="button" @click="show = false" aria-label="Dismiss"><x-icon name="x" class="size-4 opacity-60" /></button>
                </div>
            @endif
        @endforeach
    </div>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password') && ! $errors->has('name'))
            <div class="mb-6 rounded-2xl border-2 border-coral-200 bg-coral-50 px-4 py-3 text-sm text-coral-900">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t-2 border-sky-100 bg-white/80">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:grid-cols-3">
            <div>
                <div class="flex items-center gap-2 text-base font-extrabold text-slate-900">
                    <span class="flex size-8 items-center justify-center rounded-lg bg-coral-500 text-white"><x-icon name="flag" class="size-4" /></span>
                    Flagboard
                </div>
                <p class="mt-2 max-w-xs text-sm text-slate-500">A friendly home for Capture The Flag competitions. Form a team, solve challenges, climb the scoreboard.</p>
            </div>
            <div>
                <div class="text-xs font-bold tracking-wide text-slate-500 uppercase">Explore</div>
                <ul class="mt-2 space-y-1 text-sm">
                    <li><a href="{{ route('competitions.index') }}" class="text-slate-600 hover:text-sky-700">Competitions</a></li>
                    <li><a href="{{ route('writeups.index') }}" class="text-slate-600 hover:text-sky-700">Writeups</a></li>
                    <li><a href="{{ route('home') }}#how-it-works" class="text-slate-600 hover:text-sky-700">How it works</a></li>
                </ul>
            </div>
            <div>
                <div class="text-xs font-bold tracking-wide text-slate-500 uppercase">Account</div>
                <ul class="mt-2 space-y-1 text-sm">
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-sky-700">Dashboard</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-slate-600 hover:text-sky-700">Profile</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-slate-600 hover:text-sky-700">Log in</a></li>
                        <li><a href="{{ route('register') }}" class="text-slate-600 hover:text-sky-700">Create an account</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="border-t border-sky-100 py-3 text-center text-xs text-slate-400">Flagboard, a Laravel project for PBO2.</div>
    </footer>
</body>
</html>
