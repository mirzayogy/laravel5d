
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="paw-card">
            <h2 class="fw-bold text-center">Welcome Back 🐾</h2>
            <p class="text-muted text-center">
                Masuk ke akun PawCare kamu
            </p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email"
                           value="{{ old('email') }}"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password"
                           class="form-control" required>
                </div>

                <button class="btn btn-paw w-100">
                    Login
                </button>
            </form>

            <p class="text-center mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar</a>
            </p>
        </div>
    </div>
</div>
@endsection
