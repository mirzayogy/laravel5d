@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <x-page-header title="My profile" :subtitle="$user->email.' · '.$user->role" />

    <form method="POST" action="{{ route('profile.update') }}" class="rise card max-w-xl space-y-5 p-6">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="input">
        </div>
        <div>
            <label for="phone" class="label">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->profile?->phone) }}" class="input">
        </div>
        <div>
            <label for="address" class="label">Address</label>
            <input id="address" name="address" type="text" value="{{ old('address', $user->profile?->address) }}" class="input">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@endsection
