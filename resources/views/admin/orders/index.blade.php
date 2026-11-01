@extends('layouts.admin')
@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Order Management Dashboard</h1>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Order Data Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Order Code</th>
                        <th>Customer</th>
                        <th>Merchant</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $i => $order)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $order->order_code }}</strong></td>
                        <td>{{ $order->customer->name ?? '-' }}</td>
                        <td>{{ $order->merchant->store_name ?? '-' }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                                {{ strtoupper($order->payment_method ?? '-') }} ({{ ucfirst($order->payment_status) }})
                            </span>
                        </td>
                        <td>
                            @php
                                $badge = match($order->status) {
                                    'delivered' => 'success',
                                    'processing' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>{{ $order->created_at ? $order->created_at->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-edit btn-sm"><i class="fas fa-eye"></i> Detail</a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesanan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted">No orders found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

