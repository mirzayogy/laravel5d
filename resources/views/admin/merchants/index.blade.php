@extends('layouts.admin')
@section('title', 'Merchants')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Merchant Management Dashboard</h1>
    <a href="{{ route('admin.merchants.create') }}" class="btn btn-add"><i class="fas fa-plus me-1"></i> Add Merchant</a>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Merchant Data Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Store Name</th>
                        <th>Owner</th>
                        <th>Phone</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Menus</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchants as $i => $merchant)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $merchant->store_name }}</strong></td>
                        <td>{{ $merchant->owner->name ?? '-' }}</td>
                        <td>{{ $merchant->phone_number }}</td>
                        <td><i class="fas fa-star text-warning"></i> {{ number_format($merchant->rating, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $merchant->is_open ? 'success' : 'secondary' }}">
                                {{ $merchant->is_open ? 'Open' : 'Closed' }}
                            </span>
                        </td>
                        <td><span class="badge bg-info">{{ $merchant->menus_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-edit btn-sm">Edit</a>
                            <form action="{{ route('admin.merchants.destroy', $merchant) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this merchant?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No merchants found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

