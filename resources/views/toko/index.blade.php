@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>

    <div class="cards">
        <div class="card"><div class="num">{{ $totalProduk }}</div>Produk</div>
        <div class="card"><div class="num">{{ $totalKategori }}</div>Kategori</div>
        <div class="card"><div class="num">{{ $totalPesanan }}</div>Pesanan</div>
        <div class="card"><div class="num">{{ $totalUser }}</div>Pengguna</div>
    </div>

    <h2>Produk per Kategori</h2>
    <table>
        <tr><th>Kategori</th><th>Deskripsi</th><th class="right">Jumlah Produk</th></tr>
        @foreach ($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->description }}</td>
                <td class="right">{{ $category->products_count }}</td>
            </tr>
        @endforeach
    </table>
@endsection