@extends('layouts.admin')
@section('title', 'Menus')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Menu Management Dashboard</h1>
    <a href="{{ route('admin.menus.create') }}" class="btn btn-add"><i class="fas fa-plus me-1"></i> Add Menu</a>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Menu Data Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Menu Name</th>
                        <th>Merchant</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Prep Time</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $i => $menu)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $menu->name }}</strong></td>
                        <td>{{ $menu->merchant->store_name ?? '-' }}</td>
                        <td>{{ $menu->category->name ?? '-' }}</td>
                        <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                        <td>{{ $menu->preparation_time_minutes }} min</td>
                        <td>
                            @foreach($menu->tags as $tag)
                            <span class="badge" style="background: {{ $tag->color }}">{{ $tag->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge bg-{{ $menu->is_available ? 'success' : 'secondary' }}">
                                {{ $menu->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-edit btn-sm">Edit</a>
                            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this menu?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted">No menus found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

