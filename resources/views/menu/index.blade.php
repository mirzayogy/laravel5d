@extends('layouts.app')

@section('title', 'Menu')

@section('content')
    @php
        $canOrder = auth()->check() && auth()->user()->role === 'customer';
        $visible = $categories->filter(fn ($category) => $category->menus->isNotEmpty());
    @endphp

    <section class="rise relative overflow-hidden rounded-3xl bg-brand-900 px-6 py-8 text-white sm:px-10 sm:py-10">
        <div class="absolute -right-6 -top-6 select-none text-[9rem] leading-none opacity-10">☕</div>
        <p class="text-xs font-medium uppercase tracking-widest text-brand-200">Menu hari ini</p>
        <h1 class="mt-2 max-w-xl text-3xl font-semibold tracking-tight sm:text-4xl">Kopi, teh, dan makanan favorit Nusantara.</h1>
        <p class="mt-3 max-w-lg text-sm text-brand-100">Pilih menu, atur jumlahnya, lalu kirim pesananmu. Kasir akan menyiapkannya segera.</p>
        <div class="mt-5 flex flex-wrap gap-2 text-xs text-brand-100">
            <span class="rounded-full bg-white/10 px-3 py-1">{{ $visible->sum(fn ($category) => $category->menus->count()) }} menu</span>
            <span class="rounded-full bg-white/10 px-3 py-1">{{ $visible->count() }} kategori</span>
        </div>
    </section>

    <div data-filter-root class="mt-6">
        <div class="sticky top-[57px] z-20 -mx-4 border-b border-stone-200/70 bg-stone-50/90 px-4 py-3 backdrop-blur">
            <div class="flex flex-col gap-3">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
                    <input type="search" data-filter-input placeholder="Cari menu, misalnya kopi susu atau nasi goreng" class="input pl-10" autocomplete="off">
                </div>
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button type="button" data-filter-chip="all" aria-pressed="true" class="chip chip-active">Semua</button>
                    @foreach ($visible as $category)
                        <button type="button" data-filter-chip="{{ $category->id }}" aria-pressed="false" class="chip">{{ $category->icon() }} {{ $category->name }}</button>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-stone-500">Label:</span>
                    <a href="{{ route('home') }}" class="rounded-full border px-3 py-1 {{ $tag ? 'border-stone-200 bg-white text-stone-600 hover:border-brand-300' : 'border-brand-600 bg-brand-50 text-brand-700' }}">All</a>
                    @foreach ($tags as $item)
                        <a href="{{ route('home', ['tag' => $item->name]) }}"
                           class="rounded-full border px-3 py-1 {{ $tag === $item->name ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-stone-200 bg-white text-stone-600 hover:border-brand-300' }}">{{ $item->name }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        @if ($canOrder)
            <form method="POST" action="{{ route('orders.store') }}">
                @csrf
        @endif

        <div class="mt-6 grid gap-8 {{ $canOrder ? 'lg:grid-cols-[1fr_20rem]' : '' }}">
            <div>
                @forelse ($visible as $category)
                    <section data-filter-group id="cat-{{ $category->id }}" class="mb-10 scroll-mt-56">
                        <h2 class="mb-3 flex items-center gap-2 text-lg font-semibold">
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-100 text-base">{{ $category->icon() }}</span>
                            {{ $category->name }}
                            <span class="text-sm font-normal text-stone-400">{{ $category->menus->count() }}</span>
                        </h2>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($category->menus as $menu)
                                <article id="menu-{{ $menu->id }}"
                                         data-filter-item data-menu-card
                                         data-cat="{{ $category->id }}"
                                         data-price="{{ $menu->price }}"
                                         data-name="{{ $menu->name }}"
                                         data-search="{{ \Illuminate\Support\Str::lower($menu->name.' '.$menu->description.' '.$menu->tags->pluck('name')->implode(' ')) }}"
                                         class="rise card flex flex-col justify-between p-4 transition hover:shadow-md {{ $menu->is_available ? '' : 'opacity-60' }}">
                                    <div>
                                        <div class="flex items-start justify-between gap-3">
                                            <a href="{{ route('menus.show', $menu) }}" class="font-medium text-stone-900 hover:text-brand-700">{{ $menu->name }}</a>
                                            <span class="whitespace-nowrap text-sm font-semibold text-brand-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        </div>
                                        @if ($menu->description)
                                            <p class="mt-1 line-clamp-2 text-sm text-stone-500">{{ $menu->description }}</p>
                                        @endif
                                        @if ($menu->tags->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @foreach ($menu->tags as $menuTag)
                                                    <span class="rounded-md bg-stone-100 px-1.5 py-0.5 text-xs text-stone-600">{{ $menuTag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex items-center justify-between text-sm">
                                        @if (! $menu->is_available)
                                            <span class="badge bg-red-50 text-red-700">Sold out</span>
                                        @elseif ($canOrder)
                                            <span class="text-stone-500">Quantity</span>
                                            <div class="flex items-center rounded-xl border border-stone-200">
                                                <button type="button" data-qty-step="-1" aria-label="Decrease" class="h-8 w-8 rounded-l-xl text-lg text-stone-600 hover:bg-stone-100">−</button>
                                                <input type="number" name="items[{{ $menu->id }}]" min="0" max="20" data-qty-input
                                                       value="{{ old('items.'.$menu->id, 0) }}"
                                                       class="h-8 w-10 border-x border-stone-200 text-center text-sm [appearance:textfield] focus:outline-none [&::-webkit-inner-spin-button]:appearance-none">
                                                <button type="button" data-qty-step="1" aria-label="Increase" class="h-8 w-8 rounded-r-xl text-lg text-brand-700 hover:bg-brand-50">+</button>
                                            </div>
                                        @else
                                            <span class="badge bg-emerald-50 text-emerald-700">Available</span>
                                            <a href="{{ route('menus.show', $menu) }}" class="text-xs text-brand-700 hover:underline">Details →</a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <x-empty-state icon="🍽️" title="No menu found"
                        :text="$tag ? 'No menu has the label “'.$tag.'” yet.' : 'The menu is being prepared. Please check back soon.'">
                        @if ($tag)
                            <a href="{{ route('home') }}" class="btn btn-primary">Show all menu</a>
                        @endif
                        @if (auth()->check() && auth()->user()->role === 'admin')
                            <a href="{{ route('admin.menus.create') }}" class="btn btn-ghost">Add a menu</a>
                        @endif
                    </x-empty-state>
                @endforelse

                <div data-filter-empty class="hidden">
                    <x-empty-state icon="🔍" title="Nothing matches your search" text="Try another keyword or choose a different category." />
                </div>

                @guest
                    <div class="card mt-2 flex flex-wrap items-center justify-between gap-3 p-5">
                        <p class="text-sm text-stone-600">
                            <a href="{{ route('login') }}" class="font-medium text-brand-700 underline">Log in</a> or
                            <a href="{{ route('register') }}" class="font-medium text-brand-700 underline">register</a> to place an order.
                        </p>
                    </div>
                @endguest
            </div>

            @if ($canOrder)
                <aside id="cart" data-cart class="self-start lg:sticky lg:top-56">
                    <div class="card p-5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold">Your order</h2>
                            <span class="badge bg-brand-50 text-brand-700"><span data-cart-count>0</span> items</span>
                        </div>

                        <p data-cart-empty class="mt-4 rounded-xl bg-stone-50 px-4 py-6 text-center text-sm text-stone-500">
                            Your cart is empty. Tap + on a menu to add it.
                        </p>
                        <ul data-cart-list class="mt-4 space-y-2"></ul>

                        <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4">
                            <span class="text-sm text-stone-500">Total</span>
                            <span class="text-lg font-semibold" data-cart-total>Rp 0</span>
                        </div>

                        <label for="note" class="label mt-4">Note (optional)</label>
                        <input id="note" name="note" type="text" value="{{ old('note') }}" maxlength="255" placeholder="Contoh: es batu sedikit" class="input">

                        <button type="submit" data-cart-submit class="btn btn-primary mt-4 w-full">Place order</button>
                        <button type="button" data-cart-clear class="btn btn-ghost btn-sm mt-2 w-full">Clear</button>
                    </div>
                </aside>
            @endif
        </div>

        @if ($canOrder)
            </form>

            <div data-cart-bar class="fixed inset-x-0 bottom-0 z-30 hidden border-t border-stone-200 bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-3">
                    <div class="text-sm"><span data-cart-count>0</span> items · <span class="font-semibold" data-cart-total>Rp 0</span></div>
                    <a href="#cart" class="btn btn-primary btn-sm">Review order</a>
                </div>
            </div>
            <div class="h-16 lg:hidden"></div>
        @endif
    </div>
@endsection
