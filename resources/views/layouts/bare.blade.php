<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Flagboard') - Flagboard</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='9' fill='%23ff6b4a'/%3E%3Cpath d='M10 24V8m0 0h12l-3 4.5 3 4.5H10' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="flex min-h-screen items-center justify-center bg-canvas px-4 py-10 text-slate-800 antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-24 -left-24 size-96 rounded-full bg-sky-200/50 blur-3xl"></div>
        <div class="absolute -right-32 bottom-0 size-96 rounded-full bg-coral-200/40 blur-3xl"></div>
    </div>
    @yield('content')
</body>
</html>
