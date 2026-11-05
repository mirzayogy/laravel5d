@extends('layouts.app')

@section('title', $kost->name)

@section('content')
    <h1>{{ $kost->name }}</h1>
    <p class="subtitle"><a href="{{ route('kosts.index') }}" class="btn btn-gray">&larr; Kembali ke daftar kost</a></p>

    <div class="card">
        <h2>Informasi Kost</h2>
        <dl class="detail">
            <dt>Nama</dt><dd>{{ $kost->name }}</dd>
            <dt>Alamat</dt><dd>{{ $kost->address }}</dd>
            <dt>Kota</dt><dd>{{ $kost->city }}</dd>
            <dt>Pemilik</dt><dd>{{ $kost->owner->name }} ({{ $kost->owner->email }})</dd>
            <dt>Deskripsi</dt><dd>{{ $kost->description }}</dd>
        </dl>
    </div>

    <div class="card">
        <h2>Daftar Kamar ({{ $kost->rooms->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>No. Kamar</th>
                    <th>Tipe</th>
                    <th>Harga / Bulan</th>
                    <th>Status</th>
                    <th>Fasilitas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kost->rooms as $room)
                    <tr>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ ucfirst($room->type) }}</td>
                        <td>Rp {{ number_format($room->price_per_month, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badge = match ($room->status) {
                                    'available' => 'badge-green',
                                    'occupied' => 'badge-yellow',
                                    default => 'badge-red',
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($room->status) }}</span>
                        </td>
                        <td>{{ $room->facilities->pluck('name')->join(', ') ?: '-' }}</td>
                        <td><a href="{{ route('rooms.show', $room) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada kamar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection