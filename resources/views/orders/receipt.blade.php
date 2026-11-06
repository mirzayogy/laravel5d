@extends('layouts.app')

@section('title', 'Receipt '.$order->order_number)

@section('content')
    <div class="mx-auto max-w-sm">
        <div class="mb-4 flex gap-2 print:hidden">
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">{{ __('Print receipt') }}</button>
            <a href="{{ route('orders.show', $order) }}" class="btn btn-ghost btn-sm">{{ __('Back') }}</a>
        </div>

        <div class="rise card p-6 font-mono text-xs leading-relaxed print:rounded-none print:border-0 print:shadow-none">
            <div class="text-center">
                <div class="text-base font-semibold">CafeOrder</div>
                <div>{{ __('Kopi, teh, dan makanan favorit Nusantara') }}</div>
            </div>

            <div class="my-3 border-t border-dashed border-stone-400"></div>
            <div class="flex justify-between"><span>{{ __('Order') }}</span><span>{{ $order->order_number }}</span></div>
            <div class="flex justify-between"><span>{{ __('Date') }}</span><span>{{ $order->ordered_at->format('d/m/Y H:i') }}</span></div>
            <div class="flex justify-between"><span>{{ __('Customer') }}</span><span>{{ $order->user->name }}</span></div>
            <div class="flex justify-between"><span>{{ __('Type') }}</span><span>{{ $order->order_type === 'takeaway' ? __('Takeaway') : __('Dine in') }}@if ($order->order_type === 'dine_in' && $order->table_number) · {{ $order->table_number }}@endif</span></div>

            <div class="my-3 border-t border-dashed border-stone-400"></div>
            @foreach ($order->items as $item)
                <div class="mb-1">
                    <div>{{ $item->menu->name }}</div>
                    @if ($item->note)<div class="text-stone-500">({{ __($item->note) }})</div>@endif
                    <div class="flex justify-between"><span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span><span>{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</span></div>
                </div>
            @endforeach

            <div class="my-3 border-t border-dashed border-stone-400"></div>
            <div class="flex justify-between text-sm font-semibold"><span>{{ __('TOTAL') }}</span><span>Rp {{ number_format($order->total(), 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>{{ __('Payment') }}</span><span>{{ __($methods[$order->payment_method] ?? '-') }}</span></div>
            <div class="flex justify-between"><span>{{ __('Status') }}</span><span class="uppercase">{{ $order->payment_status }}</span></div>
            @if ($order->paid_at)
                <div class="flex justify-between"><span>{{ __('Paid at') }}</span><span>{{ $order->paid_at->format('d/m/Y H:i') }}</span></div>
            @endif

            <div class="my-3 border-t border-dashed border-stone-400"></div>
            <div class="text-center">{{ __('Terima kasih, sampai jumpa lagi!') }}</div>
        </div>
    </div>
@endsection
