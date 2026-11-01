@extends('layouts.admin')
@section('title', 'Reviews')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Review Management Dashboard</h1>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Customer Review Data Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Merchant</th>
                        <th>Rating</th>
                        <th>Komentar</th>
                        <th>Balasan Merchant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $i => $review)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><code>{{ $review->order->order_code ?? '-' }}</code></td>
                        <td><strong>{{ $review->customer->name ?? '-' }}</strong></td>
                        <td>{{ $review->merchant->store_name ?? '-' }}</td>
                        <td>
                            <span class="text-warning">
                                @for($star=1; $star<=5; $star++)
                                    <i class="fas fa-star{{ $star <= $review->rating ? '' : '-o' }}"></i>
                                @endfor
                            </span>
                            <span class="fw-bold ms-1">({{ $review->rating }})</span>
                        </td>
                        <td>{{ Str::limit($review->comment, 60) }}</td>
                        <td>
                            @if($review->merchant_reply)
                                <span class="text-success"><i class="fas fa-reply me-1"></i>{{ Str::limit($review->merchant_reply, 50) }}</span>
                            @else
                                <span class="text-muted fst-italic">Belum dibalas</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus review ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No reviews found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

