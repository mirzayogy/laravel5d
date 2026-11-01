@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<h1 class="page-title mb-4">LokalBites Dashboard</h1>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card primary p-3">
            <div class="stat-label">Merchants</div>
            <div class="stat-value">{{ $stats['merchants'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card success p-3">
            <div class="stat-label">Menu Items</div>
            <div class="stat-value">{{ $stats['menus'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card info p-3">
            <div class="stat-label">Orders</div>
            <div class="stat-value">{{ $stats['orders'] }}</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card warning p-3">
            <div class="stat-label">Reviews</div>
            <div class="stat-value">{{ $stats['reviews'] }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Recent Orders</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order Code</th>
                        <th>Customer</th>
                        <th>Merchant</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td><strong>{{ $order->order_code }}</strong></td>
                        <td>{{ $order->customer->name ?? '-' }}</td>
                        <td>{{ $order->merchant->store_name ?? '-' }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
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
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

