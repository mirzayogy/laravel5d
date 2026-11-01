@extends('layouts.admin')
@section('title', isset($merchant) ? 'Edit Merchant' : 'Add Merchant')

@section('content')
<h1 class="page-title mb-4">{{ isset($merchant) ? 'Edit Merchant' : 'Add New Merchant' }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ isset($merchant) ? route('admin.merchants.update', $merchant) : route('admin.merchants.store') }}" method="POST">
            @csrf
            @if(isset($merchant)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label fw-bold">Owner</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">-- Select Owner --</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $merchant->user_id ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Store Name</label>
                    <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', $merchant->store_name ?? '') }}" required>
                    @error('store_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Slug</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $merchant->slug ?? '') }}" required>
                    @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $merchant->description ?? '') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $merchant->address ?? '') }}" required>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $merchant->phone_number ?? '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Rating</label>
                    <input type="number" name="rating" class="form-control" step="0.01" min="0" max="5" value="{{ old('rating', $merchant->rating ?? '0') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="is_open" class="form-check-input" id="is_open" value="1" {{ old('is_open', $merchant->is_open ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_open">Store is Open</label>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-add">{{ isset($merchant) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection

