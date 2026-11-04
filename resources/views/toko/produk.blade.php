@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <h1>Produk</h1>

    <table>
        <tr>
            <th>SKU</th><th>Nama</th><th>Kategori</th>
            <th class="right">Harga</th><th class="right">Stok</th>
        </tr>
        @forelse ($products as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td><span class="badge">{{ $product->category->name }}</span></td>
                <td class="right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="right">{{ $product->stock }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada produk.</td></tr>
        @endforelse
    </table>
@endsection