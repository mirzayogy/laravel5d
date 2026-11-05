@extends('layouts.app')

@section('title', $facility->name)

@section('content')
    <h1>{{ $facility->name }}</h1>
    <p class="subtitle"><a href="{{ route('facilities.index') }}" class="btn btn-gray">&larr; Kembali ke daftar fasilitas</a></p>

    <div class="card">
        <h2>Kamar dengan fasilitas ini ({{ $facility->rooms->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Kost</th>
                    <th>Tipe</th>
                    <th>Harga / Bulan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facility->rooms as $room)
                    @php
                        $badge = match ($room->status) {
                            'available' => 'badge-green',
                            'occupied' => 'badge-yellow',
                            default => 'badge-red',
                        };
                    @endphp
                    <tr>
                        <td><b>{{ $room->room_number }}</b></td>
                        <td>{{ $room->kost->name }}</td>
                        <td>{{ ucfirst($room->type) }}</td>
                        <td>Rp {{ number_format($room->price_per_month, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $badge }}">{{ ucfirst($room->status) }}</span></td>
                        <td><a href="{{ route('rooms.show', $room) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada kamar yang memakai fasilitas ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection