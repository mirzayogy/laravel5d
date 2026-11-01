@extends('layouts.admin')
@section('title', 'Order Detail #' . $order->order_code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Order Details: {{ $order->order_code }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-danger">Informasi Pemesanan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Customer:</th>
                        <td>{{ $order->customer->name ?? '-' }} ({{ $order->customer->email ?? '-' }})</td>
                    </tr>
                    <tr>
                        <th>Merchant:</th>
                        <td>{{ $order->merchant->store_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat Kirim:</th>
                        <td>{{ $order->delivery_address }}</td>
                    </tr>
                    <tr>
                        <th>Catatan:</th>
                        <td>{{ $order->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><span class="badge bg-primary">{{ ucfirst($order->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-danger">Pembayaran & Review</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Total Pembayaran:</th>
                        <td><strong class="text-success fs-5">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <th>Biaya Pengiriman:</th>
                        <td>Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Metode Bayar:</th>
                        <td>{{ strtoupper($order->payment_method ?? '-') }} ({{ ucfirst($order->payment_status) }})</td>
                    </tr>
                    <tr>
                        <th>Review Customer:</th>
                        <td>
                            @if($order->review)
                                <div>
                                    <span class="text-warning">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fas fa-star{{ $i <= $order->review->rating ? '' : '-o' }}"></i>
                                        @endfor
                                    </span> ({{ $order->review->rating }}/5)
                                </div>
                                <p class="mb-0 text-muted fst-italic">"{{ $order->review->comment }}"</p>
                            @else
                                <span class="text-muted">Belum ada review</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Item yang Dipesan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu Item</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Catatan Khusus</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><strong>{{ $item->menu->name ?? 'Menu tidak ditemukan' }}</strong></td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td><span class="badge bg-secondary">{{ $item->quantity }}x</span></td>
                        <td>{{ $item->special_notes ?? '-' }}</td>
                        <td><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

