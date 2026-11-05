<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'customer') {
            return $this->customer($user);
        }

        return $this->staff($user);
    }

    private function customer(User $user): View
    {
        $orders = $user->orders()->with('items')->latest('ordered_at')->latest('id')->get();

        $stats = [
            'orders' => $orders->count(),
            'active' => $orders->whereIn('status', ['pending', 'preparing'])->count(),
            'spent' => $orders->where('status', 'completed')->sum(fn (Order $order) => $order->total()),
        ];

        $favorites = $user->orderItems()
            ->with('menu')
            ->get()
            ->groupBy('menu_id')
            ->map(fn ($items) => ['menu' => $items->first()->menu, 'quantity' => $items->sum('quantity')])
            ->sortByDesc('quantity')
            ->take(3)
            ->values();

        $recent = $orders->take(5);

        $suggestions = Menu::where('is_available', true)->inRandomOrder()->take(3)->get();

        return view('dashboard.customer', compact('user', 'stats', 'favorites', 'recent', 'suggestions'));
    }

    private function staff(User $user): View
    {
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $revenue = (int) OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

        $stats = [
            'today' => Order::where('ordered_at', '>=', now()->startOfDay())->count(),
            'open' => (int) ($statusCounts['pending'] ?? 0) + (int) ($statusCounts['preparing'] ?? 0),
            'completed' => (int) ($statusCounts['completed'] ?? 0),
            'revenue' => $revenue,
        ];

        $days = collect(range(6, 0))->map(fn (int $ago) => now()->subDays($ago)->startOfDay());

        $perDay = Order::where('ordered_at', '>=', $days->first())
            ->get(['ordered_at'])
            ->groupBy(fn (Order $order) => $order->ordered_at->format('Y-m-d'));

        $chart = $days->map(fn ($day) => [
            'label' => $day->format('D'),
            'date' => $day->format('d M'),
            'count' => $perDay->get($day->format('Y-m-d'), collect())->count(),
        ]);

        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(quantity) as sold'))
            ->groupBy('menu_id')
            ->orderByDesc('sold')
            ->with('menu')
            ->take(5)
            ->get();

        $recent = Order::with(['user', 'items'])->latest('ordered_at')->latest('id')->take(6)->get();

        $counts = $user->role === 'admin'
            ? [
                'Menus' => Menu::count(),
                'Categories' => Category::count(),
                'Tags' => Tag::count(),
                'Users' => User::count(),
            ]
            : [];

        return view('dashboard.staff', compact('user', 'stats', 'statusCounts', 'chart', 'topMenus', 'recent', 'counts'));
    }
}
