@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <h1>Detail Pembayaran</h1>
    <p class="subtitle"><a href="{{ route('payments.index') }}" class="btn btn-gray">&larr; Kembali ke daftar pembayaran</a></p>

    <div class="grid-2">
        <div class="card">
            <h2>Rincian Pembayaran</h2>
            <dl class="detail">
                <dt>Jenis</dt><dd>{{ $payment->typeLabel() }}</dd>
                <dt>Jumlah</dt><dd>Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                <dt>Jatuh Tempo</dt><dd>{{ $payment->due_date->format('d M Y') }}</dd>
                <dt>Metode</dt><dd>{{ $payment->methodLabel() }}</dd>
                <dt>Status</dt><dd><span class="badge {{ $payment->statusBadge() }}">{{ $payment->statusLabel() }}</span></dd>
                <dt>Tanggal Bayar</dt><dd>{{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : '-' }}</dd>
                <dt>Bukti Bayar</dt><dd>{{ $payment->proof_path ? 'Terlampir' : '-' }}</dd>
                <dt>Diverifikasi oleh</dt><dd>{{ $payment->verifier ? $payment->verifier->name : '-' }}</dd>
                <dt>Tanggal Verifikasi</dt><dd>{{ $payment->verified_at ? $payment->verified_at->format('d M Y, H:i') : '-' }}</dd>
                <dt>Catatan</dt><dd>{{ $payment->notes ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card">
            <h2>Asal Pembayaran</h2>
            <dl class="detail">
                <dt>Penyewa</dt><dd><a href="{{ route('tenants.show', $payment->booking->tenant) }}">{{ $payment->booking->tenant->name }}</a></dd>
                <dt>Kost</dt><dd>{{ $payment->booking->room->kost->name }}</dd>
                <dt>Kamar</dt><dd><a href="{{ route('rooms.show', $payment->booking->room) }}">{{ $payment->booking->room->room_number }}</a></dd>
                <dt>Booking</dt><dd><a href="{{ route('bookings.show', $payment->booking) }}">{{ $payment->booking->code }}</a></dd>
            </dl>
        </div>
    </div>
@endsection