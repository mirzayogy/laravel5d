@extends('layouts.app')

@section('title', 'Manage menu')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Manage menu</h1>
        <a href="{{ route('admin.menus.create') }}" class="rounded bg-amber-600 px-3 py-2 text-sm text-white hover:bg-amber-500">New menu</a>
    </div>

    <div class="mt-4 overflow-x-auto rounded border border-stone-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-100 text-left text-stone-600">
                <tr>
                    <th class="px-3 py-2">Name</th>
                    <th class="px-3 py-2">Category</th>
                    <th class="px-3 py-2">Tags</th>
                    <th class="px-3 py-2 text-right">Price</th>
                    <th class="px-3 py-2">Available</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($menus as $menu)
                    <tr class="border-t border-stone-100">
                        <td class="px-3 py-2 font-medium">{{ $menu->name }}</td>
                        <td class="px-3 py-2">{{ $menu->category->name }}</td>
                        <td class="px-3 py-2 text-stone-500">{{ $menu->tags->pluck('name')->implode(', ') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td class="px-3 py-2">{{ $menu->is_available ? 'Yes' : 'No' }}</td>
                        <td class="flex justify-end gap-2 px-3 py-2">
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="rounded border border-stone-300 px-2 py-1 hover:bg-stone-100">Edit</a>
                            <form method="POST" action="{{ route('admin.menus.destroy', $menu) }}" onsubmit="return confirm('Delete this menu?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded border border-red-300 px-2 py-1 text-red-700 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-stone-500">No menu yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
