@extends('layouts.app')

@section('breadcrumb')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="mb-0 fs-3">Program Studi</h1>
        </div>
        <div class="col-sm-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item active" aria-current="page">Program Studi</li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap align-items-center gap-2">
            <h3 class="card-title me-auto">Program Studi</h3>
            <a href="{{ route('laporan.prodi') }}" target="_blank" class="btn btn-sm btn-danger">
                <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> Cetak PDF
            </a>
            <a href="{{ route('prodi.create') }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle me-1" aria-hidden="true"></i> Tambah Data
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Kode</th>
                        <th>Nama Prodi</th>
                        <th>Nama Kaprodi</th>
                        <th style="width: 40px">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($semua_prodi as $prodi)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $prodi->kode_prodi }}</td>
                            <td>{{ $prodi->nama_prodi }}</td>
                            <td>{{ $prodi->nama_kaprodi }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('prodi.edit', $prodi) }}" class="btn btn-outline-secondary"
                                        aria-label="Ubah {{ $prodi->nama_prodi }}">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#modal-hapus-{{ $prodi->id }}"
                                        aria-label="Hapus {{ $prodi->nama_prodi }}">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                                @include('prodi.modal-hapus')
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary">Belum ada data prodi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
