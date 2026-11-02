
@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row align-items-center py-5">
    <div class="col-lg-7">
        <span class="badge rounded-pill text-bg-light mb-3">
            🐾 Your Pet, Our Care
        </span>

        <h1 class="display-4 fw-bold">
            Happy Pets,
            <span style="color:#718b75">Happy Life.</span>
        </h1>

        <p class="lead text-muted mt-3">
            PawCare membantu kamu merawat hewan kesayangan
            melalui layanan grooming yang nyaman, aman,
            dan mudah dipesan secara online.
        </p>

        <a href="{{ route('register') }}"
           class="btn btn-paw mt-3">
            Booking Grooming Sekarang
        </a>
    </div>

    <div class="col-lg-5 text-center mt-4">
        <div class="paw-card">
            <div style="font-size:100px">🐶 🐱</div>
            <h3 class="fw-bold">We Love Your Pets!</h3>
            <p class="text-muted">
                Perawatan penuh kasih untuk sahabat berbulu.
            </p>
        </div>
    </div>
</div>
@endsection
