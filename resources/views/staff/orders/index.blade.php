@extends('layouts.app')

@section('title', 'Order queue')

@section('content')
    <x-page-header title="Order queue" subtitle="Kelola status semua pesanan pelanggan." />

    <div class="rise mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('staff.orders.index') }}" class="chip {{ $status ? '' : 'chip-active' }}">All</a>
        @foreach ($statuses as $item)
            <a href="{{ route('staff.orders.index', ['status' => $item]) }}" class="chip capitalize {{ $status === $item ? 'chip-active' : '' }}">{{ $item }}</a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <x-empty-state icon="🧾" title="No order found" :text="$status ? 'There is no '.$status.' order right now.' : 'New orders from customers will appear here.'">
            @if ($status)
                <a href="{{ route('staff.orders.index') }}" class="btn btn-primary">Show all orders</a>
            @endif
        </x-empty-state>
    @else
        <div class="rise card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-stone-100 bg-stone-50">
                    <tr>
                        <th class="th">Order</th>
                        <th class="th">Customer</th>
                        <th class="th">Time</th>
                        <th class="th text-right">Total</th>
                        <th class="th">Status</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-stone-50">
                            <td class="td font-medium">{{ $order->order_number }}</td>
                            <td class="td">{{ $order->user->name }}</td>
                            <td class="td text-stone-500">{{ $order->ordered_at->format('d M Y H:i') }}</td>
                            <td class="td text-right">Rp {{ number_format($order->total(), 0, ',', '.') }}</td>
                            <td class="td">@include('partials.status-badge', ['status' => $order->status])</td>
                            <td class="td text-right"><a href="{{ route('orders.show', $order) }}" class="btn btn-ghost btn-sm">Open</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
