@extends('layouts.app')

@section('title', $tenant->name)

@section('content')
    <h1>{{ $tenant->name }}</h1>
    <p class="subtitle"><a href="{{ route('tenants.index') }}" class="btn btn-gray">&larr; Kembali ke daftar penyewa</a></p>

    <div class="card">
        <h2>Data Penyewa</h2>
        <dl class="detail">
            <dt>Nama</dt><dd>{{ $tenant->name }}</dd>
            <dt>No. HP</dt><dd>{{ $tenant->phone }}</dd>
            <dt>No. Identitas</dt><dd>{{ $tenant->id_number }}</dd>
            <dt>Pekerjaan</dt><dd>{{ $tenant->occupation }}</dd>
        </dl>
    </div>

    <div class="card">
        <h2>Riwayat Booking ({{ $tenant->bookings->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Kost</th>
                    <th>Kamar</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenant->bookings as $booking)
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
                        <td>{{ $booking->room->kost->name }}</td>
                        <td><a href="{{ route('rooms.show', $booking->room) }}">{{ $booking->room->room_number }}</a></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($booking->start_date)->format('d M Y') }}</td>
                        <td>{{ $booking->end_date ? \Illuminate\Support\Carbon::parse($booking->end_date)->format('d M Y') : '-' }}</td>
                        <td><span class="badge {{ $badge }}">{{ ucfirst($booking->status) }}</span></td>
                        <td><a href="{{ route('bookings.show', $booking) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Penyewa ini belum punya booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection