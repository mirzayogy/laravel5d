@extends('layouts.app')

@section('title', 'Fasilitas')

@section('content')
    <h1>Daftar Fasilitas</h1>
    <p class="subtitle">{{ $facilities->count() }} fasilitas tersedia di seluruh kost</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Fasilitas</th>
                    <th>Dipakai di</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facilities as $facility)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><b>{{ $facility->name }}</b></td>
                        <td><span class="badge badge-purple">{{ $facility->rooms_count }} kamar</span></td>
                        <td><a href="{{ route('facilities.show', $facility) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada fasilitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection