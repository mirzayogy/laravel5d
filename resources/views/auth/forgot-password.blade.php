<x-auth-layout title="{{ __('Forgot password') }}">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-semibold text-ink">{{ __('Forgot your password?') }}</h1>
        <p class="text-body-sm text-pewter">{{ __('Enter your email and we will send you a link to choose a new one.') }}</p>
    </div>

    @if (session('status'))
        <p role="status" class="w-full rounded-xl border border-mist/60 bg-fog px-4 py-3 text-body-sm text-ink">{{ session('status') }}</p>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="w-full space-y-4">
        @csrf
        <div>
            <label for="email" class="sr-only">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Your email') }}" autocomplete="email" autofocus
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-full">{{ __('Send reset link') }}</button>
    </form>

    <a href="{{ route('login') }}" class="text-body-sm text-pewter underline-offset-4 hover:text-ink hover:underline">{{ __('Back to log in') }}</a>
</x-auth-layout>
