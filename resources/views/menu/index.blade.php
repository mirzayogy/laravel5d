@extends('layouts.app')

@section('title', 'Menu')

@section('content')
    @php($canOrder = auth()->check() && auth()->user()->role === 'customer')

    <h1 class="text-2xl font-semibold">Menu</h1>
    <p class="mt-1 text-stone-600">Choose your drinks and food, then place an order.</p>

    <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
        <span class="text-stone-500">Filter:</span>
        <a href="{{ route('home') }}" class="rounded-full border px-3 py-1 {{ $tag ? 'border-stone-300 hover:bg-stone-100' : 'border-amber-600 bg-amber-50 text-amber-800' }}">All</a>
        @foreach ($tags as $item)
            <a href="{{ route('home', ['tag' => $item->name]) }}"
               class="rounded-full border px-3 py-1 {{ $tag === $item->name ? 'border-amber-600 bg-amber-50 text-amber-800' : 'border-stone-300 hover:bg-stone-100' }}">{{ $item->name }}</a>
        @endforeach
    </div>

    @if ($canOrder)
        <form method="POST" action="{{ route('orders.store') }}">
            @csrf
    @endif

    @foreach ($categories as $category)
        @continue($category->menus->isEmpty())

        <h2 class="mt-8 text-lg font-semibold">{{ $category->name }}</h2>
        <div class="mt-2 grid gap-3 sm:grid-cols-2">
            @foreach ($category->menus as $menu)
                <div class="rounded border border-stone-200 bg-white p-4 {{ $menu->is_available ? '' : 'opacity-60' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-medium">{{ $menu->name }}</div>
                            @if ($menu->description)
                                <p class="mt-1 text-sm text-stone-500">{{ $menu->description }}</p>
                            @endif
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach ($menu->tags as $menuTag)
                                    <span class="rounded bg-stone-200 px-1.5 py-0.5 text-xs">{{ $menuTag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="text-right text-sm font-medium">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                    </div>

                    <div class="mt-3 text-sm">
                        @if (! $menu->is_available)
                            <span class="text-red-700">Sold out</span>
                        @elseif ($canOrder)
                            <label class="flex items-center gap-2">
                                Quantity
                                <input type="number" name="items[{{ $menu->id }}]" min="0" max="20"
                                       value="{{ old('items.'.$menu->id, 0) }}"
                                       class="w-20 rounded border border-stone-300 px-2 py-1">
                            </label>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    @if ($categories->every(fn ($category) => $category->menus->isEmpty()))
        <p class="mt-8 text-sm text-stone-500">No menu found.</p>
    @endif

    @if ($canOrder)
            <div class="mt-8 max-w-xl">
                <label for="note" class="block text-sm font-medium">Note (optional)</label>
                <input id="note" name="note" type="text" value="{{ old('note') }}" maxlength="255"
                       class="mt-1 w-full rounded border border-stone-300 px-3 py-2">
                <button type="submit" class="mt-3 rounded bg-amber-600 px-4 py-2 text-sm text-white hover:bg-amber-500">Place order</button>
            </div>
        </form>
    @elseif (! auth()->check())
        <p class="mt-8 text-sm text-stone-500">
            <a href="{{ route('login') }}" class="text-amber-700 underline">Log in</a> or
            <a href="{{ route('register') }}" class="text-amber-700 underline">register</a> to place an order.
        </p>
    @endif
@endsection
