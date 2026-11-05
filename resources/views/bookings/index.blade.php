@extends('layouts.app')

@section('title', 'Booking')

@section('content')
    <h1>Daftar Booking</h1>
    <p class="subtitle">{{ $bookings->count() }} booking tercatat</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Penyewa</th>
                    <th>Kost / Kamar</th>
                    <th>Jenis Sewa</th>
                    <th>Periode</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    @php
                        $badge = match ($booking->status) {
                            'active' => 'badge-green',
                            'confirmed' => 'badge-purple',
                            'pending' => 'badge-yellow',
                            'completed' => 'badge-blue',
                            default => 'badge-red',
                        };
                    @endphp
                    <tr>
                        <td><b>{{ $booking->code }}</b></td>
                        <td>{{ $booking->tenant->name }}</td>
                        <td>{{ $booking->room->kost->name }}<br><small>Kamar {{ $booking->room->room_number }}</small></td>
                        <td>{{ $booking->rental_type === 'daily' ? 'Harian' : 'Bulanan' }}</td>
                        <td>{{ $booking->start_date->format('d M Y') }}<br><small>s/d {{ $booking->end_date ? $booking->end_date->format('d M Y') : '-' }}</small></td>
                        <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $badge }}">{{ ucfirst($booking->status) }}</span></td>
                        <td><a href="{{ route('bookings.show', $booking) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection