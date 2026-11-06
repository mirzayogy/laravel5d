@extends('layouts.app')

@section('title', __('Profile'))

@section('content')
    <x-page-header :title="__('My profile')" :subtitle="$user->email.' · '.__($user->role)" />

    <form method="POST" action="{{ route('profile.update') }}" class="rise card max-w-xl space-y-5 p-6">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="label">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="input">
        </div>
        <div>
            <label for="phone" class="label">{{ __('Phone') }}</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->profile?->phone) }}" class="input">
        </div>
        <div>
            <label for="address" class="label">{{ __('Address') }}</label>
            <input id="address" name="address" type="text" value="{{ old('address', $user->profile?->address) }}" class="input">
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </form>
@endsection
