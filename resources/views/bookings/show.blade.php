@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
    @php
        $bookingBadge = match ($booking->status) {
            'active' => 'badge-green',
            'confirmed' => 'badge-purple',
            'pending' => 'badge-yellow',
            'completed' => 'badge-blue',
            default => 'badge-red',
        };
        $unit = $booking->rental_type === 'daily' ? 'hari' : 'bulan';
        $bills = $booking->payments->where('type', '!=', 'refund');
        $totalPaid = $bills->where('status', 'paid')->sum('amount');
        $totalUnpaid = $bills->where('status', '!=', 'paid')->sum('amount');
    @endphp

    <h1>Detail Booking</h1>
    <p class="subtitle"><a href="{{ route('bookings.index') }}" class="btn btn-gray">&larr; Kembali ke daftar booking</a></p>

    <div class="grid-2">
        <div class="card">
            <h2>Informasi Booking</h2>
            <dl class="detail">
                <dt>Kode</dt><dd>{{ $booking->code }}</dd>
                <dt>Penyewa</dt><dd><a href="{{ route('tenants.show', $booking->tenant) }}">{{ $booking->tenant->name }}</a></dd>
                <dt>Kost</dt><dd>{{ $booking->room->kost->name }}</dd>
                <dt>Kamar</dt><dd><a href="{{ route('rooms.show', $booking->room) }}">{{ $booking->room->room_number }}</a></dd>
                <dt>Jenis Sewa</dt><dd>{{ $booking->rental_type === 'daily' ? 'Harian' : 'Bulanan' }}</dd>
                <dt>Durasi</dt><dd>{{ $booking->duration }} {{ $unit }}</dd>
                <dt>Mulai</dt><dd>{{ $booking->start_date->format('d M Y') }}</dd>
                <dt>Selesai</dt><dd>{{ $booking->end_date ? $booking->end_date->format('d M Y') : '-' }}</dd>
                <dt>Check-in</dt><dd>{{ $booking->checked_in_at ? $booking->checked_in_at->format('d M Y, H:i') : '-' }}</dd>
                <dt>Check-out</dt><dd>{{ $booking->checked_out_at ? $booking->checked_out_at->format('d M Y, H:i') : '-' }}</dd>
                <dt>Status</dt><dd><span class="badge {{ $bookingBadge }}">{{ ucfirst($booking->status) }}</span></dd>
            </dl>
        </div>

        <div class="card">
            <h2>Rincian Biaya</h2>
            <dl class="detail">
                <dt>Tarif per {{ $unit }}</dt><dd>Rp {{ number_format($booking->price_per_unit, 0, ',', '.') }}</dd>
                <dt>Deposit</dt><dd>Rp {{ number_format($booking->deposit_amount, 0, ',', '.') }}</dd>
                <dt>Total sewa</dt><dd>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd>
                <dt>Sudah dibayar</dt><dd>Rp {{ number_format($totalPaid, 0, ',', '.') }}</dd>
                <dt>Belum lunas</dt><dd>Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <h2>Daftar Pembayaran ({{ $booking->payments->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Jenis</th>
                    <th>Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($booking->payments as $payment)
                    <tr>
                        <td>{{ $payment->typeLabel() }}</td>
                        <td>{{ $payment->due_date->format('d M Y') }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>{{ $payment->methodLabel() }}</td>
                        <td><span class="badge {{ $payment->statusBadge() }}">{{ $payment->statusLabel() }}</span></td>
                        <td><a href="{{ route('payments.show', $payment) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection