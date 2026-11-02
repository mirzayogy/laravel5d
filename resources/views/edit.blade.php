
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Edit Data -->
    <div class="row">
        <div class="col-md-12">

            <div class="card mb-4">

                <!-- Card Header -->
                <div class="card-header">
                    <div class="row g-2 align-items-center">

                        <div class="col-12 col-md-6">
                            <h3 class="card-title">Form Edit Data</h3>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-md-end">
                                <a href="{{ route('tables') }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-left-circle me-1"></i>
                                    Kembali ke Tables
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body">

                    <form>

                        <!-- Nama -->
                        <div class="mb-3">
                            <label for="new-user-name" class="form-label">
                                Nama Lengkap
                            </label>

                            <input type="text"
                                class="form-control"
                                id="new-user-name"
                                required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="new-user-email" class="form-label">
                                Alamat Email
                            </label>

                            <input type="email"
                                class="form-control"
                                id="new-user-email"
                                required>

                            <div class="form-text">
                                Masukkan alamat email pengguna.
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="new-user-role" class="form-label">
                                Role Pengguna
                            </label>

                            <select id="new-user-role" class="form-select">
                                <option value="customer" selected>
                                    Customer
                                </option>
                                <option value="admin">
                                    Admin
                                </option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="user-status"
                                checked>

                            <label class="form-check-label" for="user-status">
                                Pengguna Aktif
                            </label>
                        </div>

                        <!-- Tombol -->
                        <div class="d-flex justify-content-end gap-2">

                            <a href="{{ route('tables') }}"
                                class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Batal
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>
                                Simpan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

@endsection
