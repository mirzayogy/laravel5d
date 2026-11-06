@extends('layouts.app')

@section('title', __('Users'))

@section('content')
    <x-page-header :title="__('Users')" :subtitle="__('Kelola akun pelanggan, kasir, dan admin.')">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ __('New user') }}</a>
    </x-page-header>

    <form method="GET" action="{{ route('admin.users.index') }}" class="rise mb-4 flex flex-wrap items-center gap-2">
        <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search name or email') }}" class="input max-w-xs">
        <select name="role" class="input w-auto" onchange="this.form.requestSubmit()">
            <option value="">{{ __('All roles') }}</option>
            @foreach ($roles as $item)
                <option value="{{ $item }}" @selected($role === $item)>{{ __($item) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-ghost">{{ __('Search') }}</button>
        @if ($search !== '' || $role)
            <a href="{{ route('admin.users.index') }}" class="text-sm text-brand-700 hover:underline">{{ __('Reset') }}</a>
        @endif
    </form>

    @if ($users->isEmpty())
        <x-empty-state icon="👤" :title="__('No user found')" :text="__('Try a different search or filter, or add a new user.')">
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">{{ __('Reset filters') }}</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ __('New user') }}</a>
        </x-empty-state>
    @else
        <div class="rise card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-stone-100 bg-stone-50">
                    <tr>
                        <th class="th">{{ __('Name') }}</th>
                        <th class="th">{{ __('Email') }}</th>
                        <th class="th">{{ __('Role') }}</th>
                        <th class="th text-right">{{ __('Orders') }}</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($users as $item)
                        <tr class="hover:bg-stone-50">
                            <td class="td font-medium">
                                {{ $item->name }}
                                @if ($item->id === auth()->id())<span class="badge ml-1 bg-brand-50 text-brand-700">{{ __('You') }}</span>@endif
                            </td>
                            <td class="td text-stone-500">{{ $item->email }}</td>
                            <td class="td">
                                <span class="badge {{ match ($item->role) { 'admin' => 'bg-purple-50 text-purple-700', 'cashier' => 'bg-sky-50 text-sky-700', default => 'bg-stone-100 text-stone-600' } }}">{{ __($item->role) }}</span>
                            </td>
                            <td class="td text-right">{{ $item->orders_count }}</td>
                            <td class="td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $item) }}" class="btn btn-ghost btn-sm">{{ __('Edit') }}</a>
                                    @if ($item->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $item) }}" data-confirm="{{ __('Delete :name?', ['name' => $item->name]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
