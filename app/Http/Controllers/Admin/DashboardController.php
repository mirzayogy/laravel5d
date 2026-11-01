<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'merchants' => Merchant::count(),
            'menus' => Menu::count(),
            'orders' => Order::count(),
            'reviews' => Review::count(),
        ];

        $recentOrders = Order::with(['customer', 'merchant'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}

