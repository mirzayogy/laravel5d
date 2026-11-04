<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class TokoController extends Controller
{
    public function index()
    {
        return view('toko.index', [
            'totalProduk' => Product::count(),
            'totalKategori' => Category::count(),
            'totalPesanan' => Order::count(),
            'totalUser' => User::count(),
            'categories' => Category::withCount('products')->get(),
        ]);
    }

    public function produk()
    {
        $products = Product::with('category')->orderBy('name')->get();

        return view('toko.produk', compact('products'));
    }

    public function pesanan()
    {
        $orders = Order::with(['user.profile', 'products'])->latest('ordered_at')->get();

        return view('toko.pesanan', compact('orders'));
    }
}
