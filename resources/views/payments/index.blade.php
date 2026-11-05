@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    @php
        $real = $payments->where('type', '!=', 'refund');
    @endphp

    <h1>Daftar Pembayaran</h1>
    <p class="subtitle">{{ $payments->count() }} pembayaran tercatat</p>

    <div class="stats">
        <div class="stat s3">
            <div class="ic">💰</div>
            <div class="num">Rp {{ number_format($real->where('status', 'paid')->sum('amount') / 1000000, 1, ',', '.') }} jt</div>
            <div class="lbl">Sudah Dibayar</div>
        </div>
        <div class="stat s4">
            <div class="ic">⏳</div>
            <div class="num">Rp {{ number_format($real->where('status', '!=', 'paid')->sum('amount') / 1000000, 1, ',', '.') }} jt</div>
            <div class="lbl">Belum Lunas</div>
        </div>
        <div class="stat s2">
            <div class="ic">🔍</div>
            <div class="num">{{ $payments->where('status', 'waiting_verification')->count() }}</div>
            <div class="lbl">Menunggu Verifikasi</div>
        </div>
        <div class="stat s1">
            <div class="ic">⚠️</div>
            <div class="num">{{ $payments->where('status', 'overdue')->count() }}</div>
            <div class="lbl">Terlambat</div>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Penyewa</th>
                    <th>Kamar</th>
                    <th>Jenis</th>
                    <th>Jatuh Tempo</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><b>{{ $payment->booking->tenant->name }}</b></td>
                        <td>{{ $payment->booking->room->room_number }}</td>
                        <td>{{ $payment->typeLabel() }}</td>
                        <td>{{ $payment->due_date->format('d M Y') }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>{{ $payment->methodLabel() }}</td>
                        <td><span class="badge {{ $payment->statusBadge() }}">{{ $payment->statusLabel() }}</span></td>
                        <td><a href="{{ route('payments.show', $payment) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty">Belum ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection