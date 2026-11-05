@extends('layouts.app')

@section('title', 'Kamar ' . $room->room_number)

@section('content')
    @php
        $roomBadge = match ($room->status) {
            'available' => 'badge-green',
            'occupied' => 'badge-yellow',
            default => 'badge-red',
        };
    @endphp

    <h1>Kamar {{ $room->room_number }}</h1>
    <p class="subtitle"><a href="{{ route('rooms.index') }}" class="btn btn-gray">&larr; Kembali ke daftar kamar</a></p>

    <div class="grid-2">
        <div class="card">
            <h2>Informasi Kamar</h2>
            <dl class="detail">
                <dt>Nomor Kamar</dt><dd>{{ $room->room_number }}</dd>
                <dt>Kost</dt><dd><a href="{{ route('kosts.show', $room->kost) }}">{{ $room->kost->name }}</a></dd>
                <dt>Tipe</dt><dd>{{ ucfirst($room->type) }}</dd>
                <dt>Lantai</dt><dd>{{ $room->floor }}</dd>
                <dt>Ukuran</dt><dd>{{ $room->size_m2 }} m²</dd>
                <dt>Harga / Hari</dt><dd>Rp {{ number_format($room->price_per_day, 0, ',', '.') }}</dd>
                <dt>Harga / Bulan</dt><dd>Rp {{ number_format($room->price_per_month, 0, ',', '.') }}</dd>
                <dt>Status</dt><dd><span class="badge {{ $roomBadge }}">{{ ucfirst($room->status) }}</span></dd>
            </dl>
        </div>

        <div class="card">
            <h2>Fasilitas ({{ $room->facilities->count() }})</h2>
            @forelse ($room->facilities as $facility)
                <span class="pill">{{ $facility->name }}</span>
            @empty
                <div class="empty">Belum ada fasilitas.</div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <h2>Riwayat Booking ({{ $room->bookings->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($room->bookings as $booking)
                    @php
                        $bookingBadge = match ($booking->status) {
                            'active' => 'badge-green',
                            'pending' => 'badge-yellow',
                            'completed' => 'badge-blue',
                            'confirmed' => 'badge-purple',
                            default => 'badge-red',
                        };
                    @endphp
                    <tr>
                        <td><a href="{{ route('tenants.show', $booking->tenant) }}">{{ $booking->tenant->name }}</a></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($booking->start_date)->format('d M Y') }}</td>
                        <td>{{ $booking->end_date ? \Illuminate\Support\Carbon::parse($booking->end_date)->format('d M Y') : '-' }}</td>
                        <td><span class="badge {{ $bookingBadge }}">{{ ucfirst($booking->status) }}</span></td>
                        <td><a href="{{ route('bookings.show', $booking) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Kamar ini belum pernah dibooking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection