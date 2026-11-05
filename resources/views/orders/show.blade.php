@extends('layouts.app')

@section('title', $order->order_number)

@section('content')
    <x-page-header :title="$order->order_number" :subtitle="$order->ordered_at->format('d M Y H:i').' · '.$order->user->name">
        @include('partials.status-badge', ['status' => $order->status])
    </x-page-header>

    @php($steps = ['pending', 'preparing', 'completed'])
    @if ($order->status === 'cancelled')
        <div class="rise mb-6 rounded-xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-600">This order was cancelled.</div>
    @else
        <ol class="rise mb-6 flex items-center gap-2 text-xs">
            @foreach ($steps as $step)
                @php($reached = array_search($order->status, $steps, true) >= $loop->index)
                <li class="flex flex-1 items-center gap-2">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full {{ $reached ? 'bg-brand-600 text-white' : 'bg-stone-200 text-stone-500' }}">{{ $reached ? '✓' : $loop->iteration }}</span>
                    <span class="capitalize {{ $reached ? 'font-medium text-stone-800' : 'text-stone-400' }}">{{ $step }}</span>
                    @unless ($loop->last)<span class="h-px grow {{ $reached ? 'bg-brand-300' : 'bg-stone-200' }}"></span>@endunless
                </li>
            @endforeach
        </ol>
    @endif

    @if ($order->note)
        <p class="rise mb-4 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-800">Note: {{ $order->note }}</p>
    @endif

    <div class="rise card overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="border-b border-stone-100 bg-stone-50">
                <tr>
                    <th class="th">Menu</th>
                    <th class="th text-right">Price</th>
                    <th class="th text-right">Qty</th>
                    <th class="th text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="td font-medium">{{ $item->menu->name }}</td>
                        <td class="td text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="td text-right">{{ $item->quantity }}</td>
                        <td class="td text-right">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t border-stone-200 bg-stone-50 font-semibold">
                    <td colspan="3" class="td text-right">Total</td>
                    <td class="td text-right">Rp {{ number_format($order->total(), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        @if ($isStaff)
            @foreach ($nextStatuses as $next)
                <form method="POST" action="{{ route('staff.orders.status', $order) }}" @if ($next === 'cancelled') data-confirm="Cancel order {{ $order->order_number }}?" @endif>
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next }}">
                    <button type="submit" class="btn btn-sm {{ $next === 'cancelled' ? 'btn-danger' : 'btn-primary' }}">Mark as {{ $next }}</button>
                </form>
            @endforeach
            @if (auth()->user()->role === 'admin' && in_array($order->status, ['completed', 'cancelled'], true))
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" data-confirm="Delete order {{ $order->order_number }} permanently?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete order</button>
                </form>
            @endif
        @elseif ($order->status === 'pending')
            <form method="POST" action="{{ route('orders.cancel', $order) }}" data-confirm="Cancel order {{ $order->order_number }}?">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Cancel order</button>
            </form>
        @endif
        <a href="{{ $isStaff ? route('staff.orders.index') : route('orders.index') }}" class="btn btn-ghost btn-sm">Back</a>
    </div>
@endsection
