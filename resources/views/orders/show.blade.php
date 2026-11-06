@extends('layouts.app')

@section('title', $order->order_number)

@section('content')
    <div data-order-poll="{{ route('orders.state', $order) }}" data-status="{{ $order->status }}" data-payment="{{ $order->payment_status }}"></div>
    <div id="live-toast" class="fixed inset-x-0 top-20 z-50 mx-auto hidden max-w-sm rounded-xl border border-brand-200 bg-white px-4 py-3 text-center text-sm shadow-lg">
        {{ __('Order updated. Refreshing…') }}
    </div>

    <x-page-header :title="$order->order_number" :subtitle="$order->ordered_at->format('d M Y H:i').' · '.$order->user->name">
        @include('partials.status-badge', ['status' => $order->status])
        @include('partials.payment-badge', ['status' => $order->payment_status])
    </x-page-header>

    @php($steps = ['pending', 'preparing', 'completed'])
    @if ($order->status === 'cancelled')
        <div class="rise mb-6 rounded-xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-600">{{ __('This order was cancelled.') }}</div>
    @else
        <ol class="rise mb-6 flex items-center gap-2 text-xs">
            @foreach ($steps as $step)
                @php($reached = array_search($order->status, $steps, true) >= $loop->index)
                <li class="flex flex-1 items-center gap-2">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full {{ $reached ? 'bg-brand-600 text-white' : 'bg-stone-200 text-stone-500' }}">{{ $reached ? '✓' : $loop->iteration }}</span>
                    <span class="capitalize {{ $reached ? 'font-medium text-stone-800' : 'text-stone-400' }}">{{ __($step) }}</span>
                    @unless ($loop->last)<span class="h-px grow {{ $reached ? 'bg-brand-300' : 'bg-stone-200' }}"></span>@endunless
                </li>
            @endforeach
        </ol>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
        <div>
            @if ($order->note)
                <p class="rise mb-4 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-800">{{ __('Note') }}: {{ $order->note }}</p>
            @endif

            <div class="rise card overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-stone-100 bg-stone-50">
                        <tr>
                            <th class="th">{{ __('Menu') }}</th>
                            <th class="th text-right">{{ __('Price') }}</th>
                            <th class="th text-right">{{ __('Qty') }}</th>
                            <th class="th text-right">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="td">
                                    <div class="flex items-center gap-3">
                                        <x-menu-photo :menu="$item->menu" class="h-10 w-10 shrink-0 rounded-lg" />
                                        <div>
                                            <div class="font-medium">{{ $item->menu->name }}</div>
                                            @if ($item->note)
                                                <div class="text-xs text-stone-500">{{ __($item->note) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="td text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="td text-right">{{ $item->quantity }}</td>
                                <td class="td text-right">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-stone-200 bg-stone-50 font-semibold">
                            <td colspan="3" class="td text-right">{{ __('Total') }}</td>
                            <td class="td text-right">Rp {{ number_format($order->total(), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <aside class="space-y-4">
            <div class="rise card p-5 text-sm">
                <h2 class="text-base font-semibold">{{ __('Order details') }}</h2>
                <dl class="mt-3 space-y-2">
                    <div class="flex justify-between"><dt class="text-stone-500">{{ __('Order type') }}</dt><dd class="font-medium">{{ $order->order_type === 'takeaway' ? __('Takeaway') : __('Dine in') }}</dd></div>
                    @if ($order->order_type === 'dine_in')
                        <div class="flex justify-between"><dt class="text-stone-500">{{ __('Table number') }}</dt><dd class="font-medium">{{ $order->table_number ?: '-' }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="rise card p-5 text-sm">
                <h2 class="text-base font-semibold">{{ __('Payment') }}</h2>
                <dl class="mt-3 space-y-2">
                    <div class="flex justify-between"><dt class="text-stone-500">{{ __('Method') }}</dt><dd class="font-medium">{{ __($methods[$order->payment_method] ?? '-') }}</dd></div>
                    <div class="flex items-center justify-between"><dt class="text-stone-500">{{ __('Status') }}</dt><dd>@include('partials.payment-badge', ['status' => $order->payment_status])</dd></div>
                    @if ($order->paid_at)
                        <div class="flex justify-between"><dt class="text-stone-500">{{ __('Paid at') }}</dt><dd class="font-medium">{{ $order->paid_at->format('d M Y H:i') }}</dd></div>
                    @endif
                    @if ($order->payment_reference && $order->payment_method !== 'cash')
                        <div class="flex justify-between gap-3"><dt class="text-stone-500">{{ __('Reference') }}</dt><dd class="break-all text-right font-mono text-xs">{{ $order->payment_reference }}</dd></div>
                    @endif
                </dl>

                @if (! $isStaff && ! $order->isPaid() && $order->status !== 'cancelled')
                    @if ($order->payment_method === 'cash')
                        <p class="mt-4 rounded-xl bg-stone-50 px-3 py-2 text-xs text-stone-600">{{ __('Pay in cash at the cashier. Show your order number :number.', ['number' => $order->order_number]) }}</p>
                        <a href="{{ route('orders.payment', $order) }}" class="btn btn-ghost btn-sm mt-3 w-full">{{ __('Pay online instead') }}</a>
                    @else
                        <a href="{{ route('orders.payment', $order) }}" class="btn btn-primary mt-4 w-full">{{ __('Pay now') }}</a>
                    @endif
                @endif

                @if ($isStaff && ! $order->isPaid() && $order->status !== 'cancelled')
                    <form method="POST" action="{{ route('staff.orders.pay', $order) }}" class="mt-4 space-y-3 border-t border-stone-100 pt-4">
                        @csrf
                        @if ($order->payment_method === 'cash' || ! $order->payment_method)
                            <label for="cash_received" class="label">{{ __('Cash received (Rp)') }}</label>
                            <input id="cash_received" name="cash_received" type="number" min="0" step="500" placeholder="{{ $order->total() }}" class="input">
                        @endif
                        <button type="submit" class="btn btn-primary w-full">{{ __('Confirm payment') }}</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        @if ($isStaff)
            @foreach ($nextStatuses as $next)
                <form method="POST" action="{{ route('staff.orders.status', $order) }}" @if ($next === 'cancelled') data-confirm="{{ __('Cancel order :number?', ['number' => $order->order_number]) }}" @endif>
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next }}">
                    <button type="submit" class="btn btn-sm {{ $next === 'cancelled' ? 'btn-danger' : 'btn-primary' }}">{{ __('Mark as :status', ['status' => __($next)]) }}</button>
                </form>
            @endforeach
            @if (auth()->user()->role === 'admin' && in_array($order->status, ['completed', 'cancelled'], true))
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" data-confirm="{{ __('Delete order :number permanently?', ['number' => $order->order_number]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete order') }}</button>
                </form>
            @endif
        @elseif ($order->status === 'pending')
            <form method="POST" action="{{ route('orders.cancel', $order) }}" data-confirm="{{ __('Cancel order :number?', ['number' => $order->order_number]) }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">{{ __('Cancel order') }}</button>
            </form>
        @endif
        <a href="{{ route('orders.receipt', $order) }}" class="btn btn-ghost btn-sm">{{ __('Receipt') }}</a>
        <a href="{{ $isStaff ? route('staff.orders.index') : route('orders.index') }}" class="btn btn-ghost btn-sm">{{ __('Back') }}</a>
    </div>
@endsection
