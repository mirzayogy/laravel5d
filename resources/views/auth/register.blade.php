<x-auth-layout title="{{ __('Create account') }}">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-semibold text-ink">{{ __('Create your account!') }}</h1>
        <p class="text-body-sm text-pewter">
            {{ __('Already registered?') }}
            <a href="{{ route('login') }}" class="text-ink hover:underline">{{ __('Log in') }}</a>
        </p>
    </div>

    <form action="{{ route('register') }}" method="POST" class="w-full space-y-4">
        @csrf
        <div>
            <label for="name" class="sr-only">{{ __('Name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Your name') }}" autocomplete="name" autofocus
                class="input @error('name') input-error @enderror" required>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="sr-only">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Your email') }}" autocomplete="email"
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="sr-only">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" placeholder="{{ __('Password (at least 8 characters)') }}" autocomplete="new-password"
                class="input @error('password') input-error @enderror" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="sr-only">{{ __('Confirm password') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm password') }}" autocomplete="new-password" class="input" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">{{ __('Create account') }}</button>

    </form>

    <p class="w-11/12 text-center text-caption text-pewter">
        {{ __('You acknowledge that you read, and agree, to our') }}
        <a href="{{ route('terms') }}" class="underline hover:text-ink">{{ __('Terms of Service') }}</a>
        {{ __('and our') }}
        <a href="{{ route('privacy') }}" class="underline hover:text-ink">{{ __('Privacy Policy') }}</a>.
    </p>
</x-auth-layout>
