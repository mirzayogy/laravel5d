@extends('layouts.app')

@section('title', 'Manage menu')

@section('content')
    <x-page-header title="Manage menu" subtitle="Tambah, ubah, hapus menu dan atur ketersediaannya.">
        <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">New menu</a>
    </x-page-header>

    @if ($menus->isEmpty())
        <x-empty-state icon="🍽️" title="No menu yet" text="Create your first menu so customers can start ordering.">
            <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">New menu</a>
        </x-empty-state>
    @else
        <div data-table-wrap>
            <input type="search" data-table-search="#menu-table" placeholder="Search menu, category or tag" class="input mb-4 max-w-sm">

            <div class="rise card overflow-x-auto">
                <table id="menu-table" class="min-w-full text-sm">
                    <thead class="border-b border-stone-100 bg-stone-50">
                        <tr>
                            <th class="th">Name</th>
                            <th class="th">Category</th>
                            <th class="th">Tags</th>
                            <th class="th text-right">Price</th>
                            <th class="th">Available</th>
                            <th class="th"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($menus as $menu)
                            <tr data-row="{{ \Illuminate\Support\Str::lower($menu->name.' '.$menu->category->name.' '.$menu->tags->pluck('name')->implode(' ')) }}" class="hover:bg-stone-50">
                                <td class="td font-medium"><a href="{{ route('menus.show', $menu) }}" class="hover:text-brand-700">{{ $menu->name }}</a></td>
                                <td class="td">{{ $menu->category->icon() }} {{ $menu->category->name }}</td>
                                <td class="td text-stone-500">{{ $menu->tags->pluck('name')->implode(', ') ?: '-' }}</td>
                                <td class="td text-right">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td class="td">
                                    <form method="POST" action="{{ route('admin.menus.toggle', $menu) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Click to toggle" class="badge {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                                            {{ $menu->is_available ? 'Yes' : 'No' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="td">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-ghost btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" data-confirm="Delete {{ $menu->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div data-table-empty class="mt-4 hidden">
                <x-empty-state icon="🔍" title="No menu matches your search" text="Try another keyword." />
            </div>
        </div>
    @endif
@endsection
