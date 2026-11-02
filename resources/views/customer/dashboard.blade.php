
@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<h2 class="fw-bold">Hello, {{ auth()->user()->name }}! 🐾</h2>
<p class="text-muted">Selamat datang di PawCare.</p>

<div class="row g-3 my-3">
    <div class="col-md-6">
        <div class="paw-card">
            <p>Total Hewan Peliharaan</p>
            <h2>{{ $pets }}</h2>
        </div>
    </div>

    <div class="col-md-6">
        <div class="paw-card">
            <p>Total Booking</p>
            <h2>{{ $bookings }}</h2>
        </div>
    </div>
</div>

<div class="paw-card mt-4">
    <h4>Booking Terbaru</h4>

    @forelse($latestBookings as $booking)
        <div class="border-bottom py-3">
            <strong>{{ $booking->pet->name }}</strong>
            — {{ $booking->service->name }}
            <span class="badge text-bg-secondary">
                {{ $booking->status }}
            </span>
        </div>
    @empty
        <p class="text-muted">Belum ada booking.</p>
    @endforelse
</div>
@endsection
