@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <x-page-header :title="__('Hello, :name', ['name' => $user->name])" :subtitle="__('Summary of your orders and favorite menu.')">
        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Order now') }}</a>
    </x-page-header>

    <div class="rise grid gap-4 sm:grid-cols-3">
        <div class="card p-5">
            <div class="text-sm text-stone-500">{{ __('Total orders') }}</div>
            <div class="mt-1 text-3xl font-semibold">{{ $stats['orders'] }}</div>
        </div>
        <div class="card p-5">
            <div class="text-sm text-stone-500">{{ __('Being prepared') }}</div>
            <div class="mt-1 text-3xl font-semibold">{{ $stats['active'] }}</div>
        </div>
        <div class="card p-5">
            <div class="text-sm text-stone-500">{{ __('Total spent') }}</div>
            <div class="mt-1 text-3xl font-semibold">Rp {{ number_format($stats['spent'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_20rem]">
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold">{{ __('Recent orders') }}</h2>
                @if ($recent->isNotEmpty())
                    <a href="{{ route('orders.index') }}" class="text-sm text-brand-700 hover:underline">{{ __('See all →') }}</a>
                @endif
            </div>
            @forelse ($recent as $order)
                <a href="{{ route('orders.show', $order) }}" class="card mb-3 flex items-center justify-between p-4 transition hover:shadow-md">
                    <div>
                        <div class="font-medium">{{ $order->order_number }}</div>
                        <div class="text-sm text-stone-500">{{ $order->ordered_at->format('d M Y H:i') }} · {{ $order->items->sum('quantity') }} {{ __('items') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold">Rp {{ number_format($order->total(), 0, ',', '.') }}</div>
                        @include('partials.status-badge', ['status' => $order->status])
                    </div>
                </a>
            @empty
                <x-empty-state icon="🧾" :title="__('No orders yet')" :text="__('Your first order will show up here.')">
                    <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Browse the menu') }}</a>
                </x-empty-state>
            @endforelse
        </section>

        <section>
            <h2 class="mb-3 text-lg font-semibold">{{ __('Your favorites') }}</h2>
            @forelse ($favorites as $favorite)
                <a href="{{ route('menus.show', $favorite['menu']) }}" class="card mb-3 flex items-center justify-between p-4 transition hover:shadow-md">
                    <span class="font-medium">{{ $favorite['menu']->name }}</span>
                    <span class="badge bg-brand-50 text-brand-700">{{ $favorite['quantity'] }}×</span>
                </a>
            @empty
                <p class="mb-3 text-sm text-stone-500">{{ __('Order something and your favorites will appear here. Maybe try:') }}</p>
                @forelse ($suggestions as $suggestion)
                    <a href="{{ route('menus.show', $suggestion) }}" class="card mb-3 flex items-center justify-between p-4 transition hover:shadow-md">
                        <span class="font-medium">{{ $suggestion->name }}</span>
                        <span class="text-sm text-brand-700">Rp {{ number_format($suggestion->price, 0, ',', '.') }}</span>
                    </a>
                @empty
                    <x-empty-state icon="🍽️" :title="__('No menu yet')" :text="__('The menu is being prepared.')" />
                @endforelse
            @endforelse
        </section>
    </div>
@endsection
