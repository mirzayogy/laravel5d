@extends('layouts.app')

@section('title', 'Order queue')

@section('content')
    <h1 class="text-2xl font-semibold">Order queue</h1>

    <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
        <a href="{{ route('staff.orders.index') }}" class="rounded-full border px-3 py-1 {{ $status ? 'border-stone-300 hover:bg-stone-100' : 'border-amber-600 bg-amber-50 text-amber-800' }}">All</a>
        @foreach ($statuses as $item)
            <a href="{{ route('staff.orders.index', ['status' => $item]) }}"
               class="rounded-full border px-3 py-1 {{ $status === $item ? 'border-amber-600 bg-amber-50 text-amber-800' : 'border-stone-300 hover:bg-stone-100' }}">{{ $item }}</a>
        @endforeach
    </div>

    <div class="mt-4 overflow-x-auto rounded border border-stone-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-100 text-left text-stone-600">
                <tr>
                    <th class="px-3 py-2">Order</th>
                    <th class="px-3 py-2">Customer</th>
                    <th class="px-3 py-2">Time</th>
                    <th class="px-3 py-2 text-right">Total</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t border-stone-100">
                        <td class="px-3 py-2 font-medium">{{ $order->order_number }}</td>
                        <td class="px-3 py-2">{{ $order->user->name }}</td>
                        <td class="px-3 py-2 text-stone-500">{{ $order->ordered_at->format('d M Y H:i') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($order->total(), 0, ',', '.') }}</td>
                        <td class="px-3 py-2">@include('partials.status-badge', ['status' => $order->status])</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('orders.show', $order) }}" class="rounded border border-stone-300 px-2 py-1 hover:bg-stone-100">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-stone-500">No order found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
