@extends('layouts.app')

@section('title', 'Kamar')

@section('content')
    <h1>Daftar Kamar</h1>
    <p class="subtitle">Semua kamar dari seluruh kost beserta status dan fasilitasnya</p>

    <div class="stats">
        <div class="stat s1">
            <div class="ic">🛏️</div>
            <div class="num">{{ $rooms->count() }}</div>
            <div class="lbl">Total Kamar</div>
        </div>
        <div class="stat s3">
            <div class="ic">✅</div>
            <div class="num">{{ $rooms->where('status', 'available')->count() }}</div>
            <div class="lbl">Tersedia</div>
        </div>
        <div class="stat s4">
            <div class="ic">🔑</div>
            <div class="num">{{ $rooms->where('status', 'occupied')->count() }}</div>
            <div class="lbl">Terisi</div>
        </div>
        <div class="stat s2">
            <div class="ic">🛠️</div>
            <div class="num">{{ $rooms->where('status', 'maintenance')->count() }}</div>
            <div class="lbl">Perbaikan</div>
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Kost</th>
                    <th>Tipe</th>
                    <th>Harga / Bulan</th>
                    <th>Status</th>
                    <th>Fasilitas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    @php
                        $badge = match ($room->status) {
                            'available' => 'badge-green',
                            'occupied' => 'badge-yellow',
                            default => 'badge-red',
                        };
                    @endphp
                    <tr>
                        <td><b>{{ $room->room_number }}</b></td>
                        <td><a href="{{ route('kosts.show', $room->kost) }}">{{ $room->kost->name }}</a></td>
                        <td>{{ ucfirst($room->type) }}</td>
                        <td>Rp {{ number_format($room->price_per_month, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $badge }}">{{ ucfirst($room->status) }}</span></td>
                        <td>
                            @forelse ($room->facilities as $facility)
                                <span class="pill">{{ $facility->name }}</span>
                            @empty
                                -
                            @endforelse
                        </td>
                        <td><a href="{{ route('rooms.show', $room) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada data kamar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection