<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Flagboard') - Flagboard</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center gap-x-6 gap-y-2 px-4 py-3">
            <a href="{{ route('home') }}" class="text-lg font-semibold text-indigo-700">Flagboard</a>
            <a href="{{ route('competitions.index') }}" class="text-sm hover:text-indigo-700">Competitions</a>
            <a href="{{ route('writeups.index') }}" class="text-sm hover:text-indigo-700">Writeups</a>

            @auth
                <a href="{{ route('team.show') }}" class="text-sm hover:text-indigo-700">My team</a>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.competitions.index') }}" class="text-sm hover:text-indigo-700">Manage competitions</a>
                    <a href="{{ route('admin.challenges.index') }}" class="text-sm hover:text-indigo-700">Manage challenges</a>
                @endif
                <div class="ml-auto flex items-center gap-3 text-sm">
                    <span class="text-slate-500">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded border border-slate-300 px-3 py-1 hover:bg-slate-100">Log out</button>
                    </form>
                </div>
            @else
                <div class="ml-auto flex items-center gap-3 text-sm">
                    <a href="{{ route('login') }}" class="hover:text-indigo-700">Log in</a>
                    <a href="{{ route('register') }}" class="rounded bg-indigo-600 px-3 py-1 text-white hover:bg-indigo-500">Register</a>
                </div>
            @endauth
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-8">
        @if (session('success'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
