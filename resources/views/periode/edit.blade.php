@extends('layouts.app')

@section('breadcrumb')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="mb-0 fs-3">Periode</h1>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('periode.index') }}">Periode</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-4">
                            <h3 class="card-title">Ubah Periode</h3>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <a href="{{ route('periode.index') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-left-circle me-1" aria-hidden="true"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('periode.update', $periode) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="periode" class="form-label">Periode</label>
                            <input type="text" class="form-control @error('periode') is-invalid @enderror" id="periode"
                                name="periode" value="{{ old('periode', $periode->periode) }}" />
                            @error('periode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="singkatan" class="form-label">Singkatan</label>
                            <input type="text" class="form-control @error('singkatan') is-invalid @enderror"
                                id="singkatan" name="singkatan" value="{{ old('singkatan', $periode->singkatan) }}" />
                            @error('singkatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status"
                                value="1" @checked(old('status', $periode->status)) />
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>

                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <a href="{{ route('periode.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1" aria-hidden="true"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection