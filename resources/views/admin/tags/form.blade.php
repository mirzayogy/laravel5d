@extends('layouts.admin')
@section('title', isset($tag) ? 'Edit Tag' : 'Add Tag')

@section('content')
<h1 class="page-title mb-4">{{ isset($tag) ? 'Edit Tag' : 'Add New Tag' }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ isset($tag) ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" method="POST">
            @csrf
            @if(isset($tag)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-bold">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tag->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Slug</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $tag->slug ?? '') }}" required>
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Color</label>
                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $tag->color ?? '#10B981') }}">
            </div>

            <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-add">{{ isset($tag) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection

