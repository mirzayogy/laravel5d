@extends('layouts.app')

@section('title', __('Payment'))

@section('content')
    <x-page-header :title="__('Payment')" :subtitle="$order->order_number" />

    <div class="rise mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        {{ __('This is a simulation. No real money is moved and no payment gateway is called.') }}
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            <form method="POST" action="{{ route('orders.payment.method', $order) }}" class="rise card p-5">
                @csrf
                <h2 class="text-base font-semibold">{{ __('Payment method') }}</h2>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach ($methods as $key => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $key }}" class="peer sr-only" onchange="this.form.requestSubmit()" @checked($order->payment_method === $key)>
                            <span class="flex items-center gap-3 rounded-xl border border-stone-200 px-4 py-3 text-sm transition peer-checked:border-brand-600 peer-checked:bg-brand-50 peer-focus-visible:outline-2 hover:border-brand-300">
                                <span class="text-xl">{{ ['cash' => '💵', 'qris' => '📱', 'transfer' => '🏦', 'ewallet' => '👛'][$key] }}</span>
                                <span class="font-medium">{{ __($label) }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </form>

            <div class="rise card p-5">
                <h2 class="text-base font-semibold">{{ __('How to pay') }}</h2>

                @if ($order->payment_method === 'qris')
                    <div class="mt-4 flex flex-col items-center gap-3 sm:flex-row sm:items-start">
                        <div class="w-48 shrink-0 rounded-xl border border-stone-200 p-2">{!! \App\Support\FakeQr::svg($order->payment_reference ?? $order->order_number) !!}</div>
                        <ol class="list-inside list-decimal space-y-2 text-sm text-stone-600">
                            <li>{{ __('Open any QRIS-supported app.') }}</li>
                            <li>{{ __('Scan the QR code on this page.') }}</li>
                            <li>{{ __('Check the amount, then confirm.') }}</li>
                            <li>{{ __('Press the button on the right to finish the simulation.') }}</li>
                        </ol>
                    </div>
                @elseif ($order->payment_method === 'transfer')
                    <div class="mt-4 rounded-xl bg-stone-50 p-4 text-sm">
                        <div class="text-stone-500">{{ __('Virtual account number') }}</div>
                        <div class="mt-1 flex flex-wrap items-center gap-3">
                            <span class="font-mono text-xl font-semibold tracking-wider">{{ $order->payment_reference }}</span>
                            <button type="button" data-copy="{{ $order->payment_reference }}" data-copied="{{ __('Copied') }}" class="btn btn-ghost btn-sm">{{ __('Copy') }}</button>
                        </div>
                        <p class="mt-3 text-stone-500">{{ __('Transfer the exact amount from any bank app or ATM, then press the button on the right.') }}</p>
                    </div>
                @elseif ($order->payment_method === 'ewallet')
                    <div class="mt-4 rounded-xl bg-stone-50 p-4 text-sm text-stone-600">
                        <p>{{ __('Payment code:') }} <span class="font-mono font-semibold text-stone-800">{{ $order->payment_reference }}</span></p>
                        <p class="mt-2">{{ __('Open GoPay, OVO, or DANA, choose “Bayar”, enter the code, and confirm. Then press the button on the right.') }}</p>
                    </div>
                @else
                    <div class="mt-4 rounded-xl bg-stone-50 p-4 text-sm text-stone-600">
                        {{ __('Cash is paid at the cashier. The cashier will confirm it. Choose another method above to pay online.') }} <span class="font-semibold text-stone-800">{{ $order->order_number }}</span>
                    </div>
                @endif
            </div>
        </div>

        <aside class="rise card self-start p-5 lg:sticky lg:top-24">
            <h2 class="text-base font-semibold">{{ __('Summary') }}</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-3">
                        <span class="text-stone-700">{{ $item->quantity }}× {{ $item->menu->name }}</span>
                        <span class="whitespace-nowrap">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4">
                <span class="text-sm text-stone-500">{{ __('Total') }}</span>
                <span class="text-xl font-semibold">Rp {{ number_format($order->total(), 0, ',', '.') }}</span>
            </div>

            @if ($order->payment_method !== 'cash')
                <form method="POST" action="{{ route('orders.payment.confirm', $order) }}" data-processing="{{ __('Processing…') }}" class="mt-5">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">{{ __('I have paid (simulate)') }}</button>
                </form>
            @endif
            <a href="{{ route('orders.show', $order) }}" class="btn btn-ghost mt-2 w-full">{{ __('Back to order') }}</a>
        </aside>
    </div>
@endsection
