@props(['title', 'wide' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    @if (app()->getLocale() !== 'en')
        <script>window.I18N = {!! json_encode(app('translator')->getLoader()->load(app()->getLocale(), '*', '*'), JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) !!};</script>
    @endif
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#ff5900">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-svh antialiased">
    @if (config('habitude.stage') !== 'production')
        <div class="fixed inset-x-0 top-0 z-50 flex h-7 items-center justify-center gap-2 bg-carbon px-3 text-caption font-medium text-paper" role="note">
            <span>{{ __('Beta preview, not production.') }}</span>
            <a href="{{ config('habitude.feedback_url') }}" target="_blank" rel="noopener" class="font-semibold text-ember underline-offset-2 hover:underline">{{ __('Send feedback') }}</a>
        </div>
    @endif

    <main class="flex min-h-svh items-center justify-center px-4 py-12">
        {{-- Card follows the login-3 layout: big radius, centred logo, centred heading --}}
        <div class="w-full {{ $wide ? 'max-w-2xl' : 'max-w-sm' }} rounded-[32px] border border-mist/60 bg-paper px-6 py-10 pt-14">
            <div class="flex flex-col items-center space-y-8">
                <a href="{{ url('/') }}" aria-label="{{ __('Habitude home') }}"><x-logo :large="true" /></a>
                {{ $slot }}
            </div>
        </div>
    </main>

    <nav class="fixed inset-x-0 bottom-4 flex justify-center gap-4 text-caption text-pewter" aria-label="{{ __('Language') }}">
        @foreach (config('habitude.locales') as $code => $name)
            <a href="{{ route('locale.switch', $code) }}" lang="{{ $code }}" @if (app()->getLocale() === $code) aria-current="true" class="font-semibold text-ink" @else class="hover:text-ink hover:underline" @endif>{{ $name }}</a>
        @endforeach
    </nav>

    <div id="toasts" class="toasts" aria-live="polite"></div>
</body>
</html>
