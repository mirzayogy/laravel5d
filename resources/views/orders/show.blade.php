@extends('layouts.app')

@section('title', $order->order_number)

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $order->order_number }}</h1>
        @include('partials.status-badge', ['status' => $order->status])
    </div>
    <p class="mt-1 text-sm text-stone-500">
        {{ $order->ordered_at->format('d M Y H:i') }} by {{ $order->user->name }}
    </p>
    @if ($order->note)
        <p class="mt-2 text-sm">Note: {{ $order->note }}</p>
    @endif

    <div class="mt-4 overflow-x-auto rounded border border-stone-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-stone-100 text-left text-stone-600">
                <tr>
                    <th class="px-3 py-2">Menu</th>
                    <th class="px-3 py-2 text-right">Price</th>
                    <th class="px-3 py-2 text-right">Qty</th>
                    <th class="px-3 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr class="border-t border-stone-100">
                        <td class="px-3 py-2">{{ $item->menu->name }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-stone-200 font-semibold">
                    <td colspan="3" class="px-3 py-2 text-right">Total</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($order->total(), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-4 flex flex-wrap gap-3">
        @if ($isStaff)
            @foreach ($nextStatuses as $next)
                <form method="POST" action="{{ route('staff.orders.status', $order) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next }}">
                    <button type="submit" class="rounded border border-stone-300 px-3 py-1 text-sm hover:bg-stone-100">Mark as {{ $next }}</button>
                </form>
            @endforeach
        @elseif ($order->status === 'pending')
            <form method="POST" action="{{ route('orders.cancel', $order) }}">
                @csrf
                <button type="submit" class="rounded border border-red-300 px-3 py-1 text-sm text-red-700 hover:bg-red-50">Cancel order</button>
            </form>
        @endif
        <a href="{{ $isStaff ? route('staff.orders.index') : route('orders.index') }}" class="rounded border border-stone-300 px-3 py-1 text-sm hover:bg-stone-100">Back</a>
    </div>
@endsection
