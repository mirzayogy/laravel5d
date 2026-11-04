<x-auth-layout title="{{ __('Log in') }}">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-semibold text-ink">{{ __('Welcome back!') }}</h1>
        <p class="text-body-sm text-pewter">
            {{ __('First time here?') }}
            <a href="{{ route('register') }}" class="text-ink hover:underline">{{ __('Sign up for free') }}</a>
        </p>
    </div>

    @if (session('status'))
        <p role="status" class="w-full rounded-xl border border-mist/60 bg-fog px-4 py-3 text-center text-body-sm text-ink">{{ session('status') }}</p>
    @endif

    <form action="{{ route('login') }}" method="POST" class="w-full space-y-4">
        @csrf
        <div>
            <label for="email" class="sr-only">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Your email') }}" autocomplete="email" autofocus
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="sr-only">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" placeholder="{{ __('Your password') }}" autocomplete="current-password"
                class="input @error('password') input-error @enderror" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between gap-3">
            <x-spring-check name="remember" label="{{ __('Remember me') }}" :checked="old('remember')" />
            <a href="{{ route('password.request') }}" class="text-body-sm text-pewter underline-offset-4 hover:text-ink hover:underline">{{ __('Forgot password?') }}</a>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">{{ __('Log in') }}</button>

    </form>

    <p class="w-11/12 text-center text-caption text-pewter">
        {{ __('You acknowledge that you read, and agree, to our') }}
        <a href="{{ route('terms') }}" class="underline hover:text-ink">{{ __('Terms of Service') }}</a>
        {{ __('and our') }}
        <a href="{{ route('privacy') }}" class="underline hover:text-ink">{{ __('Privacy Policy') }}</a>.
    </p>
</x-auth-layout>
