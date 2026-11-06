@extends('layouts.app')

@section('title', __('Order queue'))

@section('content')
    <div data-queue-poll="{{ route('staff.orders.poll') }}" data-latest="{{ $latestId }}"></div>

    <x-page-header :title="__('Order queue')" :subtitle="__('Kelola status dan pembayaran semua pesanan pelanggan.')" />

    <div id="new-orders-banner" class="mb-4 hidden items-center justify-between gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800 [&:not(.hidden)]:flex">
        <span><strong data-new-count>0</strong> {{ __('new order(s) arrived.') }}</span>
        <a href="{{ request()->fullUrl() }}" class="btn btn-primary btn-sm">{{ __('Refresh') }}</a>
    </div>

    <form method="GET" action="{{ route('staff.orders.index') }}" class="rise mb-4 flex flex-wrap items-center gap-2">
        <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Order number, customer, or table') }}" class="input max-w-xs">
        @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        @if ($payment)<input type="hidden" name="payment" value="{{ $payment }}">@endif
        <button type="submit" class="btn btn-ghost">{{ __('Search') }}</button>
        @if ($search !== '')
            <a href="{{ route('staff.orders.index', array_filter(['status' => $status, 'payment' => $payment])) }}" class="text-sm text-brand-700 hover:underline">{{ __('Reset') }}</a>
        @endif
    </form>

    <div class="rise mb-2 flex flex-wrap items-center gap-2">
        <a href="{{ route('staff.orders.index', array_filter(['payment' => $payment, 'q' => $search])) }}" class="chip {{ $status ? '' : 'chip-active' }}">{{ __('All') }}</a>
        @foreach ($statuses as $item)
            <a href="{{ route('staff.orders.index', array_filter(['status' => $item, 'payment' => $payment, 'q' => $search])) }}" class="chip capitalize {{ $status === $item ? 'chip-active' : '' }}">{{ __($item) }}</a>
        @endforeach
    </div>
    <div class="rise mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('staff.orders.index', array_filter(['status' => $status, 'q' => $search])) }}" class="chip {{ $payment ? '' : 'chip-active' }}">{{ __('Any payment') }}</a>
        @foreach (['unpaid', 'paid', 'refunded'] as $item)
            <a href="{{ route('staff.orders.index', array_filter(['status' => $status, 'payment' => $item, 'q' => $search])) }}" class="chip capitalize {{ $payment === $item ? 'chip-active' : '' }}">{{ __($item) }}</a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <x-empty-state icon="🧾" :title="__('No order found')" :text="__('Try another filter, or wait for new orders from customers.')">
            <a href="{{ route('staff.orders.index') }}" class="btn btn-primary">{{ __('Show all orders') }}</a>
        </x-empty-state>
    @else
        <div class="rise card overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-stone-100 bg-stone-50">
                    <tr>
                        <th class="th">{{ __('Order') }}</th>
                        <th class="th">{{ __('Customer') }}</th>
                        <th class="th">{{ __('Type') }}</th>
                        <th class="th">{{ __('Time') }}</th>
                        <th class="th text-right">{{ __('Total') }}</th>
                        <th class="th">{{ __('Status') }}</th>
                        <th class="th">{{ __('Payment') }}</th>
                        <th class="th"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-stone-50">
                            <td class="td font-medium">{{ $order->order_number }}</td>
                            <td class="td">{{ $order->user->name }}</td>
                            <td class="td text-stone-500">{{ $order->order_type === 'takeaway' ? __('Takeaway') : __('Dine in') }}@if ($order->order_type === 'dine_in' && $order->table_number) · {{ $order->table_number }}@endif</td>
                            <td class="td text-stone-500">{{ $order->ordered_at->format('d M Y H:i') }}</td>
                            <td class="td text-right">Rp {{ number_format($order->total(), 0, ',', '.') }}</td>
                            <td class="td">@include('partials.status-badge', ['status' => $order->status])</td>
                            <td class="td">@include('partials.payment-badge', ['status' => $order->payment_status])</td>
                            <td class="td text-right"><a href="{{ route('orders.show', $order) }}" class="btn btn-ghost btn-sm">{{ __('Open') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6">{{ $orders->links() }}</div>
@endsection
