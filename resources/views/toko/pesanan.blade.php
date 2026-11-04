@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <h1>Pesanan</h1>

    @forelse ($orders as $order)
        <div class="order card">
            <strong>{{ $order->order_number }}</strong>
            <span class="badge">{{ $order->status }}</span>
            <p>
                Pembeli: {{ $order->user->name }}
                ({{ $order->user->profile->city ?? '-' }}),
                tanggal: {{ $order->ordered_at?->format('d M Y H:i') }}
            </p>

            <table>
                <tr>
                    <th>Produk</th><th class="right">Jumlah</th>
                    <th class="right">Harga Satuan</th><th class="right">Subtotal</th>
                </tr>
                @foreach ($order->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="right">{{ $product->pivot->quantity }}</td>
                        <td class="right">Rp {{ number_format($product->pivot->unit_price, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($product->pivot->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="3" class="right">Total</th>
                    <th class="right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                </tr>
            </table>
        </div>
    @empty
        <p>Belum ada pesanan.</p>
    @endforelse
@endsection