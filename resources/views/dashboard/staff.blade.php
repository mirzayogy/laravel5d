@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    @php($max = max(1, (int) $chart->max("count")))

    <x-page-header :title="__('Hello, :name', ['name' => $user->name])" :subtitle="$user->role === 'admin' ? __('Overview of CafeOrder operations.') : __('Overview of today\'s order queue.')">
        <a href="{{ route('staff.orders.index') }}" class="btn btn-primary">{{ __('Open order queue') }}</a>
        @if ($user->role === 'admin')
            <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">{{ __('Reports') }}</a>
            <a href="{{ route('admin.menus.create') }}" class="btn btn-ghost">{{ __('Add menu') }}</a>
        @endif
    </x-page-header>

    <div class="rise grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Orders today') }}</div><div class="mt-1 text-3xl font-semibold">{{ $stats['today'] }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Open orders') }}</div><div class="mt-1 text-3xl font-semibold">{{ $stats['open'] }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Completed') }}</div><div class="mt-1 text-3xl font-semibold">{{ $stats['completed'] }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Revenue (completed)') }}</div><div class="mt-1 text-2xl font-semibold">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div></div>
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-2">
        <section class="card p-5">
            <h2 class="text-base font-semibold">{{ __('Orders, last 7 days') }}</h2>
            <div class="mt-5 flex h-40 items-end gap-3">
                @foreach ($chart as $bar)
                    <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1" title="{{ $bar['date'] }}: {{ $bar['count'] }} orders">
                        <span class="text-xs text-stone-500 opacity-0 transition group-hover:opacity-100">{{ $bar['count'] }}</span>
                        <div class="w-full rounded-t-lg bg-brand-300 transition group-hover:bg-brand-600" style="height: {{ max(4, round($bar['count'] / $max * 100)) }}%"></div>
                        <span class="text-xs text-stone-500">{{ $bar['label'] }}</span>
                    </div>
                @endforeach
            </div>
            @if ($chart->sum('count') === 0)
                <p class="mt-3 text-center text-sm text-stone-500">{{ __('No orders in the last 7 days.') }}</p>
            @endif
        </section>

        <section class="card p-5">
            <h2 class="text-base font-semibold">{{ __('Best sellers') }}</h2>
            @forelse ($topMenus as $row)
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span><span class="mr-2 text-stone-400">{{ $loop->iteration }}.</span>{{ $row->menu->name }}</span>
                    <span class="badge bg-brand-50 text-brand-700">{{ __(':count sold', ['count' => $row->sold]) }}</span>
                </div>
            @empty
                <x-empty-state class="mt-3 border-0 py-8" icon="📈" :title="__('No sales yet')" :text="__('Best sellers appear after the first order.')" />
            @endforelse
        </section>
    </div>

    @if ($counts)
        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($counts as $label => $count)
                <a href="{{ route('admin.'.strtolower($label).'.index') }}" class="card p-5 transition hover:shadow-md">
                    <div class="text-sm text-stone-500">{{ $label }}</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $count }}</div>
                </a>
            @endforeach
        </div>
    @endif

    <section class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold">{{ __('Latest orders') }}</h2>
            @if ($recent->isNotEmpty())
                <a href="{{ route('staff.orders.index') }}" class="text-sm text-brand-700 hover:underline">{{ __('See all →') }}</a>
            @endif
        </div>
        @forelse ($recent as $order)
            <a href="{{ route('orders.show', $order) }}" class="card mb-3 flex items-center justify-between p-4 transition hover:shadow-md">
                <div>
                    <div class="font-medium">{{ $order->order_number }} · {{ $order->user->name }}</div>
                    <div class="text-sm text-stone-500">{{ $order->ordered_at->format('d M Y H:i') }} · {{ $order->items->sum('quantity') }} {{ __('items') }}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold">Rp {{ number_format($order->total(), 0, ',', '.') }}</div>
                    @include('partials.status-badge', ['status' => $order->status])
                </div>
            </a>
        @empty
            <x-empty-state icon="🧾" :title="__('No orders yet')" :text="__('New orders from customers will appear here.')" />
        @endforelse
    </section>
@endsection
