@extends('layouts.app')

@section('header')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="mb-0 fs-3">Periode</h1>
                </div>
                <div class="col-sm-6">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item active" aria-current="page">Periode</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <h3 class="card-title">Data Periode</h3>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modal-add-user">
                                <i class="bi bi-plus-circle me-1" aria-hidden="true"> </i>
                                Tambah Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Periode</th>
                            <th>Singkatan</th>
                            <th>Status</th>
                            <th style="width: 40px">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semua_periode as $periode)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $periode->periode }}</td>
                            <td>{{ $periode->singkatan}}</td>
                            <td>{{ $periode->status}}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('edit') }}" type="button" class="btn btn-outline-secondary"
                                        aria-label="Edit Daniel Cooper">
                                        <i class="bi bi-pencil" aria-hidden="true"> </i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#modal-delete-user" aria-label="Delete Daniel Cooper">
                                        <i class="bi bi-trash" aria-hidden="true"> </i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
@endsection
