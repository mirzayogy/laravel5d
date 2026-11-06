@extends('layouts.app')

@section('title', __('Verify email'))

@section('content')
    <div class="rise mx-auto max-w-md">
        <x-empty-state icon="✉️" :title="__('Verify your email')" :text="__('We sent a verification link to your email address. Open it to activate ordering.')">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary">{{ __('Resend link') }}</button>
            </form>
            @if (app()->environment('local'))
                <form method="POST" action="{{ route('verification.demo') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">{{ __('Verify now (demo)') }}</button>
                </form>
            @endif
            <a href="{{ route('home') }}" class="btn btn-ghost">{{ __('Back to menu') }}</a>
        </x-empty-state>
        <p class="mt-4 text-center text-xs text-stone-500">{{ __('Wrong email? Log out and register again with the correct address.') }}</p>
    </div>
@endsection
