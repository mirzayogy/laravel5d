@extends('layouts.admin')
@section('title', 'Tags')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Tag Management Dashboard</h1>
    <a href="{{ route('admin.tags.create') }}" class="btn btn-add"><i class="fas fa-plus me-1"></i> Add Tag</a>
</div>

<div class="card">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-danger">Tag Data Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tag Name</th>
                        <th>Slug</th>
                        <th>Color</th>
                        <th>Menus</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $i => $tag)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><span class="badge" style="background: {{ $tag->color }}">{{ $tag->name }}</span></td>
                        <td><code>{{ $tag->slug }}</code></td>
                        <td><span style="display:inline-block;width:20px;height:20px;border-radius:4px;background:{{ $tag->color }}"></span> {{ $tag->color }}</td>
                        <td><span class="badge bg-secondary">{{ $tag->menus_count }}</span></td>
                        <td>
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-edit btn-sm">Edit</a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this tag?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-delete btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">No tags found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

