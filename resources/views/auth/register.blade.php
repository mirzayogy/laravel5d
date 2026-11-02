
@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="paw-card">
            <h2 class="fw-bold">Join PawCare 🐾</h2>
            <p class="text-muted">Buat akun untuk memesan grooming.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input name="name" class="form-control"
                           value="{{ old('name') }}" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email"
                           class="form-control"
                           value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password"
                           class="form-control" minlength="8" required>
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" required>
                </div>

                <button class="btn btn-paw w-100">
                    Buat Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
