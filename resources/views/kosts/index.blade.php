@extends('layouts.app')

@section('title', 'Kost')

@section('content')
    <h1>Daftar Kost</h1>
    <p class="subtitle">{{ $kosts->count() }} kost terdaftar</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                    <th>Pemilik</th>
                    <th>Jumlah Kamar</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kosts as $kost)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $kost->name }}</td>
                        <td>{{ $kost->address }}</td>
                        <td>{{ $kost->city }}</td>
                        <td>{{ $kost->owner->name }}</td>
                        <td>{{ $kost->rooms->count() }}</td>
                        <td><a href="{{ route('kosts.show', $kost) }}" class="btn">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada data kost.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection