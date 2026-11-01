@extends('layouts.admin')
@section('title', isset($category) ? 'Edit Category' : 'Add Category')

@section('content')
<h1 class="page-title mb-4">{{ isset($category) ? 'Edit Category' : 'Add New Category' }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-bold">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Slug</label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug ?? '') }}" required>
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Icon (FontAwesome name)</label>
                <input type="text" name="icon" class="form-control" value="{{ old('icon', $category->icon ?? '') }}" placeholder="e.g. bowl-rice">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
            </div>

            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-add">{{ isset($category) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection

