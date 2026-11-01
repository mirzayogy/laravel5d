@extends('layouts.admin')
@section('title', isset($menu) ? 'Edit Menu' : 'Add Menu')

@section('content')
<h1 class="page-title mb-4">{{ isset($menu) ? 'Edit Menu' : 'Add New Menu' }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ isset($menu) ? route('admin.menus.update', $menu) : route('admin.menus.store') }}" method="POST">
            @csrf
            @if(isset($menu)) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Merchant</label>
                    <select name="merchant_id" class="form-select @error('merchant_id') is-invalid @enderror" required>
                        <option value="">-- Select Merchant --</option>
                        @foreach($merchants as $merchant)
                        <option value="{{ $merchant->id }}" {{ old('merchant_id', $menu->merchant_id ?? '') == $merchant->id ? 'selected' : '' }}>{{ $merchant->store_name }}</option>
                        @endforeach
                    </select>
                    @error('merchant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Menu Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $menu->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Slug</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $menu->slug ?? '') }}" required>
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $menu->description ?? '') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Price (Rp)</label>
                    <input type="number" name="price" class="form-control" min="0" value="{{ old('price', $menu->price ?? '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Preparation Time (min)</label>
                    <input type="number" name="preparation_time_minutes" class="form-control" min="1" value="{{ old('preparation_time_minutes', $menu->preparation_time_minutes ?? '') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_available" class="form-check-input" id="is_available" value="1" {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_available">Available</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Tags</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                    <div class="form-check">
                        <input type="checkbox" name="tags[]" class="form-check-input" id="tag_{{ $tag->id }}" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tags', isset($menu) ? $menu->tags->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tag_{{ $tag->id }}">
                            <span class="badge" style="background: {{ $tag->color }}">{{ $tag->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-add">{{ isset($menu) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection

