@extends('layouts.app')

@section('title', 'Manage users')

@section('content')
    <x-page-header title="Users" :subtitle="$total.' accounts, '.$admins.' admins'" icon="user" />

    @include('partials.admin-nav')

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-56 grow">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by name or email" class="w-full rounded-full border-2 border-sky-100 bg-white py-2.5 pr-4 pl-10 text-sm focus:border-sky-400 focus:outline-none">
            </div>
            <select name="role" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">Any role</option>
                <option value="admin" @selected($role === 'admin')>Admins</option>
                <option value="player" @selected($role === 'player')>Players</option>
            </select>
            <select name="verified" class="rounded-full border-2 border-sky-100 bg-white px-4 py-2.5 text-sm font-semibold focus:border-sky-400 focus:outline-none">
                <option value="">Verified or not</option>
                <option value="yes" @selected($verified === 'yes')>Verified</option>
                <option value="no" @selected($verified === 'no')>Not verified</option>
            </select>
            <x-button type="submit" icon="search">Filter</x-button>
        </form>
    </x-card>

    <x-card :padded="false" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50 text-left text-xs font-bold tracking-wide text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Team</th>
                        <th class="px-4 py-3 text-right">Solves</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-t border-sky-50 hover:bg-sky-50/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$user->name" size="sm" />
                                    <div class="min-w-0">
                                        <div class="truncate font-bold">{{ $user->name }}</div>
                                        <div class="truncate text-xs text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $user->team?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $user->solves_count }}</td>
                            <td class="px-4 py-3">
                                @if ($user->hasVerifiedEmail())
                                    <x-badge tone="green"><x-icon name="check" class="size-3" /> Verified</x-badge>
                                @else
                                    <form method="POST" action="{{ route('admin.users.verify', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        <x-badge tone="coral">Not verified</x-badge>
                                        <button class="text-xs font-bold text-sky-700 hover:underline">Verify</button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($user->id === auth()->id())
                                    <x-badge tone="amber">{{ $user->role }} (you)</x-badge>
                                @else
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" aria-label="Role of {{ $user->name }}" class="rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm font-semibold">
                                            @foreach (['player', 'admin'] as $option)
                                                <option value="{{ $option }}" @selected($user->role === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        <x-button type="submit" size="sm" variant="soft" icon="check">Save</x-button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" size="sm" variant="danger" icon="trash">Delete</x-button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mt-6">{{ $users->links('pagination.custom') }}</div>
@endsection
