@extends('layouts.app')

@section('breadcrumb')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="mb-0 fs-3">Program Studi</h1>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('prodi.index') }}">Program Studi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Ubah Prodi</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('prodi.update', $prodi) }}" method="POST">
                @csrf
                @method('PUT')
                @include('prodi.form')

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('prodi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1" aria-hidden="true"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
