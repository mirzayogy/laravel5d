
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<h2 class="fw-bold mb-2">Dashboard Admin 🐾</h2>
<p class="text-muted mb-4">
    Selamat datang, {{ auth()->user()->name }}
</p>

@php
$stats = [
    ['Customer', $customers],
    ['Groomer', $groomers],
    ['Hewan', $pets],
    ['Layanan', $services],
    ['Booking', $bookings],
];
@endphp

<div class="row g-3">
    @foreach($stats as [$label, $value])
        <div class="col-md-4">
            <div class="paw-card">
                <p class="text-muted">{{ $label }}</p>
                <h2 class="fw-bold">{{ $value }}</h2>
            </div>
        </div>
    @endforeach

    <div class="col-md-4">
        <div class="paw-card">
            <p class="text-muted">Total Pendapatan</p>
            <h2 class="fw-bold">
                Rp{{ number_format($revenue, 0, ',', '.') }}
            </h2>
        </div>
    </div>
</div>
@endsection
