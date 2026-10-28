@extends('layouts.app')

@section('title', 'My orders')

@section('content')
    <h1 class="text-2xl font-semibold">My orders</h1>

    <div class="mt-4 space-y-3">
        @forelse ($orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="block rounded border border-stone-200 bg-white p-4 hover:border-amber-300">
                <div class="flex items-center justify-between">
                    <span class="font-medium">{{ $order->order_number }}</span>
                    @include('partials.status-badge', ['status' => $order->status])
                </div>
                <div class="mt-1 text-sm text-stone-500">
                    {{ $order->ordered_at->format('d M Y H:i') }},
                    {{ $order->items->sum('quantity') }} items,
                    Rp {{ number_format($order->total(), 0, ',', '.') }}
                </div>
            </a>
        @empty
            <p class="text-sm text-stone-500">
                You have no orders yet. <a href="{{ route('home') }}" class="text-amber-700 underline">Open the menu</a> to order.
            </p>
        @endforelse
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
