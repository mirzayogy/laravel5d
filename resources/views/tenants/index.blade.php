@extends('layouts.app')

@section('title', 'Penyewa')

@section('content')
    <h1>Daftar Penyewa</h1>
    <p class="subtitle">{{ $tenants->count() }} penyewa terdaftar</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>No. HP</th>
                    <th>No. Identitas</th>
                    <th>Pekerjaan</th>
                    <th>Booking</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenants as $tenant)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><b>{{ $tenant->name }}</b></td>
                        <td>{{ $tenant->phone }}</td>
                        <td>{{ $tenant->id_number }}</td>
                        <td>{{ $tenant->occupation }}</td>
                        <td><span class="badge badge-purple">{{ $tenant->bookings_count }}</span></td>
                        <td><a href="{{ route('tenants.show', $tenant) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada penyewa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection