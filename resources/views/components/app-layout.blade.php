@props(['title', 'crumbs' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidebar="closed">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}{{ request()->routeIs('admin.*') ? ' Admin' : '' }}</title>

    {{-- Set the sidebar state before first paint to avoid a flash --}}
    <script>
        try {
            var s = localStorage.getItem('habitude.sidebar');
            document.documentElement.dataset.sidebar =
                window.matchMedia('(min-width: 1024px)').matches && s !== 'closed' ? 'open' : 'closed';
        } catch (e) {}
    </script>

    @if (app()->getLocale() !== 'en')
        <script>window.I18N = {!! json_encode(app('translator')->getLoader()->load(app()->getLocale(), '*', '*'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) !!};</script>
    @endif
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#ff5900">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Habitude">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-svh antialiased" data-reminders-url="{{ route('reminders.today') }}" style="--bar-h: {{ request()->routeIs('admin.*') || config('habitude.stage') !== 'production' ? '1.75rem' : '0px' }}">
    @php
        $user = auth()->user();
        $admin = request()->routeIs('admin.*');
        $preview = config('habitude.stage') !== 'production';
        $bar = $admin || $preview;

        if ($admin) {
            $nav = [
                ['label' => __('Overview'), 'url' => route('admin.overview'), 'active' => request()->routeIs('admin.overview')],
                ['label' => __('Users'), 'url' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
                ['label' => __('Master Data'), 'open' => true, 'children' => [
                    ['label' => __('Categories'), 'icon' => 'folder', 'url' => route('admin.categories.index'), 'active' => request()->routeIs('admin.categories.*')],
                    ['label' => __('Tags'), 'icon' => 'tag', 'url' => route('admin.tags.index'), 'active' => request()->routeIs('admin.tags.*')],
                    ['label' => __('Achievements'), 'icon' => 'trophy', 'url' => route('admin.achievements.index'), 'active' => request()->routeIs('admin.achievements.*')],
                ]],
                ['label' => __('Back to app'), 'url' => route('dashboard'), 'active' => false],
            ];
            $dock = [
                ['label' => __('Overview'), 'icon' => 'layout-grid', 'url' => route('admin.overview'), 'active' => request()->routeIs('admin.overview')],
                ['label' => __('Users'), 'icon' => 'users', 'url' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
                ['label' => __('Categories'), 'icon' => 'folder', 'url' => route('admin.categories.index'), 'active' => request()->routeIs('admin.categories.*', 'admin.tags.*', 'admin.achievements.*')],
                ['label' => __('Back to app'), 'icon' => 'arrow-left', 'url' => route('dashboard'), 'active' => false],
                ['label' => __('Menu'), 'icon' => 'menu', 'toggle' => true],
            ];
        } else {
            $nav = [
                ['label' => __('Dashboard'), 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
                ['label' => __('Track'), 'open' => true, 'children' => [
                    ['label' => __('Habits'), 'icon' => 'target', 'url' => route('habits.index'), 'active' => request()->routeIs('habits.*')],
                    ['label' => __('Mood'), 'icon' => 'smile', 'url' => route('mood.index'), 'active' => request()->routeIs('mood.*')],
                    ['label' => __('Journal'), 'icon' => 'book-open', 'url' => route('journal.index'), 'active' => request()->routeIs('journal.*')],
                    ['label' => __('Achievements'), 'icon' => 'trophy', 'url' => route('achievements.index'), 'active' => request()->routeIs('achievements.*')],
                ]],
            ];
            $dock = [
                ['label' => __('Dashboard'), 'icon' => 'home', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
                ['label' => __('Habits'), 'icon' => 'target', 'url' => route('habits.index'), 'active' => request()->routeIs('habits.*')],
                ['label' => __('Mood'), 'icon' => 'smile', 'url' => route('mood.index'), 'active' => request()->routeIs('mood.*')],
                ['label' => __('Journal'), 'icon' => 'book-open', 'url' => route('journal.index'), 'active' => request()->routeIs('journal.*')],
                ['label' => __('Menu'), 'icon' => 'menu', 'toggle' => true],
            ];
        }

        // Everything the command palette can do or open
        $palette = [
            ['group' => __('Go to'), 'label' => __('Dashboard'), 'url' => route('dashboard'), 'keywords' => 'home today'],
            ['group' => __('Go to'), 'label' => __('Habits'), 'url' => route('habits.index'), 'keywords' => 'list manage'],
            ['group' => __('Go to'), 'label' => __('Mood'), 'url' => route('mood.index'), 'keywords' => 'feelings history'],
            ['group' => __('Go to'), 'label' => __('Journal'), 'url' => route('journal.index'), 'keywords' => 'diary entries'],
            ['group' => __('Go to'), 'label' => __('Achievements'), 'url' => route('achievements.index'), 'keywords' => 'badges trophies'],
            ['group' => __('Go to'), 'label' => __('Profile'), 'url' => route('profile.edit'), 'keywords' => 'account settings password reminders notifications'],
            ['group' => __('Actions'), 'label' => __('Add a habit'), 'action' => 'quick-add', 'fallback' => route('habits.index', ['add' => 1]), 'keywords' => 'new create'],
            ['group' => __('Actions'), 'label' => __('Log my mood'), 'url' => route('mood.index'), 'keywords' => 'feel rate'],
            ['group' => __('Actions'), 'label' => __('Write in my journal'), 'url' => route('journal.create'), 'keywords' => 'diary new entry'],
            ['group' => __('Actions'), 'label' => __('Toggle the sidebar'), 'action' => 'sidebar', 'keywords' => 'menu navigation'],
            ['group' => __('Actions'), 'label' => __('Install Habitude as an app'), 'action' => 'install', 'keywords' => 'pwa phone home screen'],
            ['group' => __('Actions'), 'label' => __('Log out'), 'action' => 'logout', 'keywords' => 'sign out'],
        ];
        if ($user->isAdmin()) {
            array_push($palette,
                ['group' => __('Admin'), 'label' => __('Admin overview'), 'url' => route('admin.overview')],
                ['group' => __('Admin'), 'label' => __('Manage users'), 'url' => route('admin.users.index')],
                ['group' => __('Admin'), 'label' => __('Categories'), 'url' => route('admin.categories.index'), 'hint' => __('Master data')],
                ['group' => __('Admin'), 'label' => __('Tags'), 'url' => route('admin.tags.index'), 'hint' => __('Master data')],
                ['group' => __('Admin'), 'label' => __('Manage achievements'), 'url' => route('admin.achievements.index'), 'hint' => __('Master data')],
            );
        }
        foreach ($user->habits()->orderBy('name')->get(['id', 'name']) as $habit) {
            $palette[] = ['group' => __('Your habits'), 'label' => $habit->name, 'hint' => __('Edit habit'), 'url' => route('habits.edit', $habit)];
        }
    @endphp

    {{-- Top announcement bar (design system): preview notice for users, console notice for admins --}}
    @if ($bar)
        <div class="fixed inset-x-0 top-0 z-50 flex h-7 items-center justify-center gap-2 bg-carbon px-3 text-caption font-medium text-paper" role="note">
            @if ($admin)
                <x-icon name="shield" class="size-3.5" />
                <span>{{ __('Admin console. Changes here affect every user.') }}</span>
                <a href="{{ route('dashboard') }}" class="font-semibold text-ember underline-offset-2 hover:underline">{{ __('Back to app') }}</a>
            @else
                <span class="max-sm:hidden">{{ __('Beta preview, not production. Data may be reset.') }}</span>
                <span class="sm:hidden">{{ __('Beta preview, not production.') }}</span>
                <a href="{{ config('habitude.feedback_url') }}" target="_blank" rel="noopener" class="font-semibold text-ember underline-offset-2 hover:underline">{{ __('Send feedback') }}</a>
            @endif
        </div>
    @endif

    {{-- Mobile overlay --}}
    <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-ink/40 in-data-[sidebar=open]:block lg:hidden!"></div>

    {{-- Sidebar (shadcn sidebar-08, inset variant) --}}
    <aside aria-label="{{ __('Sidebar') }}"
        class="fixed bottom-0 left-0 top-(--bar-h) z-40 flex w-(--sidebar-width) -translate-x-full flex-col bg-fog transition-transform duration-200 in-data-[sidebar=open]:translate-x-0 max-lg:border-r max-lg:border-mist/60 max-lg:bg-paper">
        <div class="p-4 pb-2">
            <a href="{{ $admin ? route('admin.overview') : route('dashboard') }}" class="flex items-center rounded-xl px-1 py-1" aria-label="{{ __('Habitude home') }}">
                <x-logo :badge="$admin ? 'Admin' : null" />
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4">
            <x-nav-tree :items="$nav" />
        </div>

        <div class="px-3 pb-2">
            <a href="https://github.com/dzakwannajmi/habitude" target="_blank" rel="noopener" class="menu-item">
                <x-icon name="external-link" />
                {{ __('Repository') }}
            </a>
        </div>

        {{-- User card (nav-user) --}}
        <div class="relative border-t border-mist/60 p-3">
            <details class="group" data-menu>
                <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl p-2 hover:bg-paper [&::-webkit-details-marker]:hidden">
                    <x-avatar :user="$user" size="size-8" />
                    <span class="grid flex-1 text-left leading-tight">
                        <span class="truncate text-body-sm font-semibold">{{ $user->name }}</span>
                        <span class="truncate text-caption text-pewter">{{ $user->email }}</span>
                    </span>
                    <x-icon name="chevrons-up-down" class="size-4 text-steel" />
                </summary>
                <div class="absolute inset-x-3 bottom-full mb-2 rounded-xl border border-mist/60 bg-paper p-1 shadow-[0_8px_24px_-8px_rgb(0_0_0/0.18)]">
                    <a href="{{ route('profile.edit') }}" class="menu-item"><x-icon name="user" /> {{ __('Profile') }}</a>
                    @if ($user->isAdmin())
                        <a href="{{ $admin ? route('dashboard') : route('admin.overview') }}" class="menu-item">
                            <x-icon name="{{ $admin ? 'arrow-left' : 'shield' }}" /> {{ $admin ? __('Back to app') : __('Admin console') }}
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="menu-item"><x-icon name="log-out" /> {{ __('Log out') }}</button>
                    </form>
                </div>
            </details>
        </div>
    </aside>

    {{-- Inset content --}}
    <div class="p-0 pt-(--bar-h) transition-[padding] duration-200 lg:p-2 lg:pt-[calc(var(--bar-h)+0.5rem)] lg:in-data-[sidebar=open]:pl-(--sidebar-width)">
        <div class="flex min-h-[calc(100svh-var(--bar-h))] flex-col bg-paper lg:min-h-[calc(100svh-1rem-var(--bar-h))] lg:rounded-xl lg:border lg:border-mist/60">
            <header class="flex h-14 shrink-0 items-center gap-2 border-b border-mist/60 px-4">
                <button type="button" data-sidebar-toggle class="btn btn-ghost btn-icon" aria-label="{{ __('Toggle sidebar') }}">
                    <x-icon name="panel-left" />
                </button>
                <span class="mx-1 h-4 w-px bg-mist/60" aria-hidden="true"></span>
                <nav aria-label="{{ __('Breadcrumb') }}">
                    <ol class="flex items-center gap-1.5 text-body-sm text-pewter">
                        @foreach ($crumbs as $crumb)
                            @php [$label, $url] = array_pad((array) $crumb, 2, null); @endphp
                            @if (! $loop->first)
                                <li aria-hidden="true"><x-icon name="chevron-right" class="size-3.5" /></li>
                            @endif
                            <li @if ($loop->last) aria-current="page" class="font-medium text-ink" @endif>
                                @if ($url && ! $loop->last)
                                    <a href="{{ $url }}" class="hover:text-ink">{{ $label }}</a>
                                @else
                                    {{ $label }}
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
                <button type="button" data-palette-open class="btn btn-outline btn-sm ml-auto max-sm:size-8 max-sm:p-0" aria-label="{{ __('Search and commands') }}">
                    <x-icon name="search" class="size-3.5" />
                    <span class="max-sm:hidden">{{ __('Search') }}</span>
                    <span class="kbd max-sm:hidden" data-palette-key>{{ __('Ctrl K') }}</span>
                </button>
            </header>

            <main class="mx-auto w-full max-w-(--page-max-width) flex-1 p-4 pb-28 sm:p-6 lg:pb-6">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>@csrf</form>

    <dialog id="palette" class="pal" aria-label="{{ __('Command palette') }}">
        <input type="text" class="pal-input" data-palette-input role="combobox" aria-expanded="true" aria-controls="palette-list" autocomplete="off" spellcheck="false" placeholder="{{ __('Type a page, a habit, or an action…') }}">
        <ul id="palette-list" class="pal-list" role="listbox" data-palette-list></ul>
        <p class="pal-empty" data-palette-empty hidden>{{ __('Nothing matches that.') }}</p>
        <p class="pal-foot"><span><span class="kbd">↑</span> <span class="kbd">↓</span> {{ __('to move') }}</span><span><span class="kbd">{{ __('Enter') }}</span> {{ __('to open') }}</span><span><span class="kbd">{{ __('Esc') }}</span> {{ __('to close') }}</span></p>
        <script type="application/json" id="palette-data">{!! json_encode($palette, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    </dialog>

    <x-dock :items="$dock" />
    <div id="toasts" class="toasts" aria-live="polite"></div>
</body>
</html>
