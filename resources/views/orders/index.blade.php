@extends('layouts.app')

@section('title', 'My orders')

@section('content')
    <x-page-header title="My orders" subtitle="Riwayat semua pesananmu.">
        <a href="{{ route('home') }}" class="btn btn-primary">New order</a>
    </x-page-header>

    <div class="space-y-3">
        @forelse ($orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="rise card flex items-center justify-between gap-4 p-4 transition hover:shadow-md">
                <div>
                    <div class="font-medium">{{ $order->order_number }}</div>
                    <div class="mt-0.5 text-sm text-stone-500">
                        {{ $order->ordered_at->format('d M Y H:i') }} · {{ $order->items->sum('quantity') }} items
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold">Rp {{ number_format($order->total(), 0, ',', '.') }}</div>
                    @include('partials.status-badge', ['status' => $order->status])
                </div>
            </a>
        @empty
            <x-empty-state icon="🧾" title="You have no orders yet" text="Pick your favorite drinks and food, then place your first order.">
                <a href="{{ route('home') }}" class="btn btn-primary">Open the menu</a>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
