@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <h1 class="text-2xl font-semibold">My profile</h1>
    <p class="mt-1 text-sm text-stone-500">{{ $user->email }} ({{ $user->role }})</p>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-4 max-w-md space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                   class="mt-1 w-full rounded border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->profile?->phone) }}"
                   class="mt-1 w-full rounded border border-stone-300 px-3 py-2">
        </div>
        <div>
            <label for="address" class="block text-sm font-medium">Address</label>
            <input id="address" name="address" type="text" value="{{ old('address', $user->profile?->address) }}"
                   class="mt-1 w-full rounded border border-stone-300 px-3 py-2">
        </div>
        <button type="submit" class="rounded bg-amber-600 px-3 py-2 text-sm text-white hover:bg-amber-500">Save</button>
    </form>
@endsection
