@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="page-title">
        <h1>Produk</h1>
        <p>Daftar produk beserta kategori, harga, dan stok</p>
    </div>

    <div class="panel">
        <div class="panel-head">
            <span>Semua Produk</span>
            <span class="badge cat-0">{{ $products->count() }} produk</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>SKU</th><th>Nama</th><th>Kategori</th>
                        <th class="right">Harga</th><th class="right">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><code>{{ $product->sku }}</code></td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td><span class="badge cat-{{ $product->category_id % 5 }}">{{ $product->category->name }}</span></td>
                            <td class="right price">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="right"><span class="stock {{ $product->stock <= 20 ? 'low' : 'ok' }}">{{ $product->stock }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection