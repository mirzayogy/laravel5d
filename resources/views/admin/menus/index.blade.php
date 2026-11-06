@extends('layouts.app')

@section('title', __('Manage menu'))

@section('content')
    <x-page-header :title="__('Manage menu')" :subtitle="__('Tambah, ubah, hapus menu, foto, stok, dan ketersediaannya.')">
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">{{ __('New menu') }}</a>
    </x-page-header>

    <form method="GET" action="{{ route('admin.menus.index') }}" class="rise mb-4 flex flex-wrap items-center gap-2">
        <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search menu or description') }}" class="input max-w-xs">
        <select name="category" class="input w-auto" onchange="this.form.requestSubmit()">
            <option value="">{{ __('All categories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="availability" class="input w-auto" onchange="this.form.requestSubmit()">
            <option value="">{{ __('Any availability') }}</option>
            <option value="available" @selected($availability === 'available')>{{ __('Available') }}</option>
            <option value="sold_out" @selected($availability === 'sold_out')>{{ __('Sold out') }}</option>
            <option value="low_stock" @selected($availability === 'low_stock')>{{ __('Low stock (5 or less)') }}</option>
        </select>
        <button type="submit" class="btn btn-ghost">{{ __('Search') }}</button>
        @if ($search !== '' || $categoryId || $availability)
            <a href="{{ route('admin.menus.index') }}" class="text-sm text-brand-700 hover:underline">{{ __('Reset') }}</a>
        @endif
    </form>

    @if ($menus->isEmpty())
        @if ($search !== '' || $categoryId || $availability)
            <x-empty-state icon="🍽️" :title="__('No menu found')" :text="__('Try different filters, or create a new menu.')">
                <a href="{{ route('admin.menus.index') }}" class="btn btn-ghost">{{ __('Reset filters') }}</a>
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">{{ __('New menu') }}</a>
            </x-empty-state>
        @else
            <x-empty-state icon="🍽️" :title="__('No menu yet')" :text="__('Create your first menu so customers can start ordering.')">
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">{{ __('New menu') }}</a>
            </x-empty-state>
        @endif
    @else
        <div class="rise card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-stone-100 bg-stone-50">
                    <tr>
                        <th class="th">{{ __('Menu') }}</th>
                        <th class="th">{{ __('Category') }}</th>
                        <th class="th">{{ __('Tags') }}</th>
                        <th class="th text-right">{{ __('Price') }}</th>
                        <th class="th text-right">{{ __('Stock') }}</th>
                        <th class="th">{{ __('Available') }}</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($menus as $menu)
                        <tr class="hover:bg-stone-50">
                            <td class="td">
                                <a href="{{ route('menus.show', $menu) }}" class="flex items-center gap-3 font-medium hover:text-brand-700">
                                    <x-menu-photo :menu="$menu" :icon="$menu->category->icon()" class="h-10 w-10 shrink-0 rounded-lg" />
                                    {{ $menu->name }}
                                </a>
                            </td>
                            <td class="td">{{ $menu->category->icon() }} {{ $menu->category->name }}</td>
                            <td class="td text-stone-500">{{ $menu->tags->pluck('name')->implode(', ') ?: '-' }}</td>
                            <td class="td text-right">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                            <td class="td text-right">
                                @if ($menu->stock === null)
                                    <span class="text-stone-400">∞</span>
                                @else
                                    <span class="{{ $menu->stock === 0 ? 'text-red-600' : ($menu->isLowStock() ? 'text-amber-600' : '') }}">{{ $menu->stock }}</span>
                                @endif
                            </td>
                            <td class="td">
                                <form method="POST" action="{{ route('admin.menus.toggle', $menu) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="{{ __('Click to toggle') }}" class="badge {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                                        {{ $menu->is_available ? __('Yes') : __('No') }}
                                    </button>
                                </form>
                            </td>
                            <td class="td">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-ghost btn-sm">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" data-confirm="{{ __('Delete :name?', ['name' => $menu->name]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6">{{ $menus->links() }}</div>
@endsection
