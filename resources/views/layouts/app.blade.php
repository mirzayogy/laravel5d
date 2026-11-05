<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Menu') - CafeOrder</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="flex min-h-screen flex-col bg-stone-50 font-sans text-stone-800">
    @php
        $user = auth()->user();
        $role = $user?->role;
        $isStaff = in_array($role, ['cashier', 'admin'], true);
        $link = fn (string $pattern) => request()->routeIs($pattern)
            ? 'bg-brand-50 text-brand-700'
            : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900';
        $menuActive = request()->routeIs('home', 'menus.show');
    @endphp

    <header class="sticky top-0 z-30 border-b border-stone-200/80 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center gap-2 px-4 py-3">
            <a href="{{ route('home') }}" class="mr-3 flex items-center gap-2 text-lg font-semibold tracking-tight text-brand-800">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-600 text-base text-white">☕</span>
                CafeOrder
            </a>

            <nav class="hidden items-center gap-1 text-sm md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-1.5 {{ $menuActive ? 'bg-brand-50 text-brand-700' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }}">Menu</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-1.5 {{ $link('dashboard') }}">Dashboard</a>
                    @if ($role === 'customer')
                        <a href="{{ route('orders.index') }}" class="rounded-lg px-3 py-1.5 {{ $link('orders.*') }}">My orders</a>
                    @endif
                    @if ($isStaff)
                        <a href="{{ route('staff.orders.index') }}" class="rounded-lg px-3 py-1.5 {{ $link('staff.orders.*') }}">Order queue</a>
                    @endif
                    @if ($role === 'admin')
                        <a href="{{ route('admin.menus.index') }}" class="rounded-lg px-3 py-1.5 {{ $link('admin.menus.*') }}">Manage menu</a>
                        <a href="{{ route('admin.categories.index') }}" class="rounded-lg px-3 py-1.5 {{ $link('admin.categories.*') }}">Categories</a>
                        <a href="{{ route('admin.tags.index') }}" class="rounded-lg px-3 py-1.5 {{ $link('admin.tags.*') }}">Tags</a>
                        <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-1.5 {{ $link('admin.users.*') }}">Users</a>
                    @endif
                @endauth
            </nav>

            <div class="ml-auto hidden items-center gap-2 text-sm md:flex">
                @auth
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-full border border-stone-200 py-1 pl-1 pr-3 hover:bg-stone-50">
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                        <span class="max-w-32 truncate">{{ $user->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-1.5 text-stone-600 hover:bg-stone-100">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
            </div>

            <button type="button" data-nav-toggle aria-label="Toggle navigation" class="ml-auto rounded-lg border border-stone-200 p-2 md:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden border-t border-stone-200 bg-white px-4 py-3 md:hidden">
            <div class="flex flex-col gap-1 text-sm">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 {{ $menuActive ? 'bg-brand-50 text-brand-700' : 'text-stone-600' }}">Menu</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 {{ $link('dashboard') }}">Dashboard</a>
                    @if ($role === 'customer')
                        <a href="{{ route('orders.index') }}" class="rounded-lg px-3 py-2 {{ $link('orders.*') }}">My orders</a>
                    @endif
                    @if ($isStaff)
                        <a href="{{ route('staff.orders.index') }}" class="rounded-lg px-3 py-2 {{ $link('staff.orders.*') }}">Order queue</a>
                    @endif
                    @if ($role === 'admin')
                        <a href="{{ route('admin.menus.index') }}" class="rounded-lg px-3 py-2 {{ $link('admin.menus.*') }}">Manage menu</a>
                        <a href="{{ route('admin.categories.index') }}" class="rounded-lg px-3 py-2 {{ $link('admin.categories.*') }}">Categories</a>
                        <a href="{{ route('admin.tags.index') }}" class="rounded-lg px-3 py-2 {{ $link('admin.tags.*') }}">Tags</a>
                        <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-2 {{ $link('admin.users.*') }}">Users</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2 {{ $link('profile.*') }}">Profile ({{ $user->name }})</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="btn btn-ghost w-full">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-stone-600">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary mt-1">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="pointer-events-none fixed inset-x-0 top-16 z-40 mx-auto flex max-w-md flex-col gap-2 px-4">
        @if (session('success'))
            <div data-toast="success" class="rise pointer-events-auto flex items-start gap-3 rounded-xl border border-green-200 bg-white px-4 py-3 text-sm shadow-lg">
                <span class="text-green-600">✓</span>
                <span class="grow text-stone-700">{{ session('success') }}</span>
                <button type="button" data-toast-close class="text-stone-400 hover:text-stone-700" aria-label="Close">×</button>
            </div>
        @endif
        @if (session('error'))
            <div data-toast="error" class="rise pointer-events-auto flex items-start gap-3 rounded-xl border border-red-200 bg-white px-4 py-3 text-sm shadow-lg">
                <span class="text-red-600">!</span>
                <span class="grow text-stone-700">{{ session('error') }}</span>
                <button type="button" data-toast-close class="text-stone-400 hover:text-stone-700" aria-label="Close">×</button>
            </div>
        @endif
    </div>

    <main class="mx-auto w-full max-w-6xl grow px-4 py-8">
        @if ($errors->any())
            <div class="rise mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-stone-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-5 text-xs text-stone-500">
            <span>© {{ date('Y') }} CafeOrder · Kopi, teh, dan makanan favorit Nusantara</span>
            <span>Buka setiap hari 08.00 – 22.00</span>
        </div>
    </footer>

    <dialog id="confirm-dialog" class="m-auto w-full max-w-sm rounded-2xl border border-stone-200 p-0 shadow-xl">
        <div class="p-6">
            <h2 class="text-base font-semibold">Are you sure?</h2>
            <p id="confirm-message" class="mt-2 text-sm text-stone-600"></p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" id="confirm-no" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="button" id="confirm-yes" class="btn btn-primary btn-sm">Yes, continue</button>
            </div>
        </div>
    </dialog>
</body>
</html>
