@extends('layouts.app')

@section('title', $menu->name)

@section('content')
    <nav class="rise mb-5 text-sm text-stone-500">
        <a href="{{ route('home') }}" class="hover:text-brand-700">Menu</a>
        <span class="mx-1">/</span>
        <span>{{ $menu->category->name }}</span>
    </nav>

    <div class="rise card overflow-hidden md:grid md:grid-cols-[18rem_1fr]">
        <div class="grid place-items-center bg-brand-50 py-12 text-8xl">{{ $menu->category->icon() }}</div>
        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge bg-brand-50 text-brand-700">{{ $menu->category->name }}</span>
                @if ($menu->is_available)
                    <span class="badge bg-emerald-50 text-emerald-700">Available</span>
                @else
                    <span class="badge bg-red-50 text-red-700">Sold out</span>
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
                @if (auth()->check() && auth()->user()->role === 'customer' && $menu->is_available)
                    <a href="{{ route('home') }}#menu-{{ $menu->id }}" class="btn btn-primary">Order this menu</a>
                @elseif (! auth()->check())
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in to order</a>
                @endif
                @if (auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-primary">Edit menu</a>
                @endif
                <a href="{{ route('home') }}" class="btn btn-ghost">Back to menu</a>
            </div>
        </div>
    </div>

    <h2 class="mb-3 mt-10 text-lg font-semibold">Lainnya di {{ $menu->category->name }}</h2>
    @if ($related->isEmpty())
        <x-empty-state icon="🍽️" title="No other menu in this category yet" text="This is the only item here for now.">
            <a href="{{ route('home') }}" class="btn btn-ghost">Browse all menu</a>
        </x-empty-state>
    @else
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($related as $item)
                <a href="{{ route('menus.show', $item) }}" class="card p-4 transition hover:shadow-md">
                    <div class="font-medium">{{ $item->name }}</div>
                    <div class="mt-1 text-sm font-semibold text-brand-700">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    @if (! $item->is_available)
                        <span class="badge mt-2 bg-red-50 text-red-700">Sold out</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection
