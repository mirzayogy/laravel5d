@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-title">
        <h1>Dashboard</h1>
        <p>Ringkasan data Toko Online Mini</p>
    </div>

    <div class="stats">
        <div class="stat blue"><span class="icon">🛍️</span><div class="num">{{ $totalProduk }}</div><div class="label">Produk</div></div>
        <div class="stat green"><span class="icon">🏷️</span><div class="num">{{ $totalKategori }}</div><div class="label">Kategori</div></div>
        <div class="stat orange"><span class="icon">🧾</span><div class="num">{{ $totalPesanan }}</div><div class="label">Pesanan</div></div>
        <div class="stat purple"><span class="icon">👥</span><div class="num">{{ $totalUser }}</div><div class="label">Pengguna</div></div>
    </div>

    @php($maxProduk = max($categories->max('products_count'), 1))

    <div class="panel">
        <div class="panel-head">Produk per Kategori</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Kategori</th><th>Deskripsi</th><th>Sebaran</th><th class="right">Jumlah Produk</th></tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td><span class="badge cat-{{ $loop->index % 5 }}">{{ $category->name }}</span></td>
                            <td>{{ $category->description }}</td>
                            <td>
                                <div class="bar">
                                    <div class="fill cat-{{ $loop->index % 5 }}" style="width: {{ ($category->products_count / $maxProduk) * 100 }}%"></div>
                                </div>
                            </td>
                            <td class="right"><strong>{{ $category->products_count }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection