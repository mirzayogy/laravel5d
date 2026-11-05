@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <div class="page-title">
        <h1>Pesanan</h1>
        <p>Daftar pesanan beserta pembeli dan item yang dibeli</p>
    </div>

    @forelse ($orders as $order)
        <div class="panel">
            <div class="order-head">
                <div class="who">
                    <span class="avatar">{{ strtoupper(substr($order->user->name, 0, 1)) }}</span>
                    <div>
                        <div class="order-no">{{ $order->order_number }}
                            <span class="badge b-{{ $order->status }}">{{ $order->status }}</span>
                        </div>
                        <div class="meta">
                            {{ $order->user->name }} · {{ $order->user->profile->city ?? '-' }}
                            · {{ $order->ordered_at?->format('d M Y H:i') }}
                        </div>
                    </div>
                </div>
                <div class="order-grand">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th><th class="right">Jumlah</th>
                            <th class="right">Harga Satuan</th><th class="right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td class="right">{{ $product->pivot->quantity }}</td>
                                <td class="right">Rp {{ number_format($product->pivot->unit_price, 0, ',', '.') }}</td>
                                <td class="right price">Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" class="right">Total</td>
                            <td class="right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="panel"><div class="panel-head">Belum ada pesanan.</div></div>
    @endforelse
@endsection