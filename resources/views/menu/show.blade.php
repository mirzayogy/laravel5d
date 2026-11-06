@extends('layouts.app')

@section('title', $menu->name)

@section('content')
    <nav class="rise mb-5 text-sm text-stone-500">
        <a href="{{ route('home') }}" class="hover:text-brand-700">{{ __('Menu') }}</a>
        <span class="mx-1">/</span>
        <span>{{ $menu->category->name }}</span>
    </nav>

    <div class="rise card overflow-hidden md:grid md:grid-cols-[18rem_1fr]">
        <x-menu-photo :menu="$menu" :icon="$menu->category->icon()" class="h-64 w-full md:h-full md:min-h-72" />
        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge bg-brand-50 text-brand-700">{{ $menu->category->name }}</span>
                @if ($menu->isOrderable())
                    <span class="badge bg-emerald-50 text-emerald-700">{{ __('Available') }}</span>
                    @if ($menu->isLowStock())
                        <span class="badge bg-amber-50 text-amber-700">{{ __('Only') }} {{ $menu->stock }} {{ __('left') }}</span>
                    @endif
                @else
                    <span class="badge bg-red-50 text-red-700">{{ __('Sold out') }}</span>
                @endif
            </div>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $menu->name }}</h1>
            <p class="mt-1 text-2xl font-semibold text-brand-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
            <p class="mt-4 text-stone-600">{{ $menu->description ?: 'Belum ada deskripsi untuk menu ini.' }}</p>

            @if ($menu->tags->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($menu->tags as $tag)
                        <a href="{{ route('home', ['tag' => $tag->name]) }}" class="chip">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 flex flex-wrap gap-2">
                @if (auth()->check() && auth()->user()->role === 'customer' && $menu->isOrderable())
                    <a href="{{ route('home') }}#menu-{{ $menu->id }}" class="btn btn-primary">{{ __('Order this menu') }}</a>
                @elseif (! auth()->check())
                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Log in to order') }}</a>
                @endif
                @if (auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-primary">{{ __('Edit menu') }}</a>
                @endif
                <a href="{{ route('home') }}" class="btn btn-ghost">{{ __('Back to menu') }}</a>
            </div>
        </div>
    </div>

    <h2 class="mb-3 mt-10 text-lg font-semibold">{{ __('Lainnya di') }} {{ $menu->category->name }}</h2>
    @if ($related->isEmpty())
        <x-empty-state icon="🍽️" :title="__('No other menu in this category yet')" :text="__('This is the only item here for now.')">
            <a href="{{ route('home') }}" class="btn btn-ghost">{{ __('Browse all menu') }}</a>
        </x-empty-state>
    @else
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($related as $item)
                <a href="{{ route('menus.show', $item) }}" class="card p-4 transition hover:shadow-md">
                    <div class="font-medium">{{ $item->name }}</div>
                    <div class="mt-1 text-sm font-semibold text-brand-700">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    @if (! $item->isOrderable())
                        <span class="badge mt-2 bg-red-50 text-red-700">{{ __('Sold out') }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection
