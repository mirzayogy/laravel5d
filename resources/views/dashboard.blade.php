@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p class="subtitle">Ringkasan data Kost Area</p>

    <div class="stats">
        <div class="stat s1">
            <div class="ic">🏘️</div>
            <div class="num">{{ $stats['kosts'] }}</div>
            <div class="lbl">Kost</div>
        </div>
        <div class="stat s2">
            <div class="ic">🛏️</div>
            <div class="num">{{ $stats['rooms'] }}</div>
            <div class="lbl">Kamar</div>
        </div>
        <div class="stat s3">
            <div class="ic">👥</div>
            <div class="num">{{ $stats['tenants'] }}</div>
            <div class="lbl">Penyewa</div>
        </div>
        <div class="stat s4">
            <div class="ic">📅</div>
            <div class="num">{{ $stats['activeBookings'] }}</div>
            <div class="lbl">Booking Aktif</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>Pendapatan</h2>
            <dl class="detail">
                <dt>Sudah dibayar</dt><dd>Rp {{ number_format($stats['paid'], 0, ',', '.') }}</dd>
                <dt>Belum dibayar</dt><dd>Rp {{ number_format($stats['unpaid'], 0, ',', '.') }}</dd>
            </dl>
        </div>
        <div class="card">
            <h2>Akses Cepat</h2>
            <a href="{{ route('kosts.index') }}" class="btn">Kost</a>
            <a href="{{ route('rooms.index') }}" class="btn">Kamar</a>
            <a href="{{ route('bookings.index') }}" class="btn">Booking</a>
            <a href="{{ route('payments.index') }}" class="btn">Pembayaran</a>
        </div>
    </div>

    <div class="card">
        <h2>Booking Terbaru</h2>
        <table>
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Kost</th>
                    <th>Kamar</th>
                    <th>Mulai</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestBookings as $booking)
                    @php
                        $badge = match ($booking->status) {
                            'active' => 'badge-green',
                            'pending' => 'badge-yellow',
                            'completed' => 'badge-blue',
                            'confirmed' => 'badge-purple',
                            default => 'badge-red',
                        };
                    @endphp
                    <tr>
                        <td><b>{{ $booking->tenant->name }}</b></td>
                        <td>{{ $booking->room->kost->name }}</td>
                        <td>{{ $booking->room->room_number }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($booking->start_date)->format('d M Y') }}</td>
                        <td><span class="badge {{ $badge }}">{{ ucfirst($booking->status) }}</span></td>
                        <td><a href="{{ route('bookings.show', $booking) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection