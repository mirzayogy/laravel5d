<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Sales figures for a date range. Revenue only counts completed orders.
 */
class ReportService
{
    private const LINE_TOTAL = 'order_items.quantity * order_items.unit_price';

    /**
     * @return array<string, mixed>
     */
    public function build(CarbonInterface $from, CarbonInterface $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $revenue = (int) $this->completedItems($from, $to)->sum(DB::raw(self::LINE_TOTAL));
        $itemsSold = (int) $this->completedItems($from, $to)->sum('order_items.quantity');
        $completed = (int) $this->completedItems($from, $to)->distinct()->count('orders.id');

        $perDay = $this->completedItems($from, $to)
            ->selectRaw('DATE(orders.ordered_at) as day, COUNT(DISTINCT orders.id) as orders, SUM('.self::LINE_TOTAL.') as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $days = collect();
        for ($cursor = $from->copy(); $cursor->lte($to); $cursor->addDay()) {
            $key = $cursor->format('Y-m-d');
            $row = $perDay->get($key);
            $days->push([
                'date' => $key,
                'label' => $cursor->format('d M'),
                'orders' => (int) ($row->orders ?? 0),
                'revenue' => (int) ($row->revenue ?? 0),
            ]);
        }

        return [
            'from' => $from,
            'to' => $to,
            'totals' => [
                'orders' => Order::whereBetween('ordered_at', [$from, $to])->count(),
                'completed' => $completed,
                'cancelled' => Order::whereBetween('ordered_at', [$from, $to])->where('status', 'cancelled')->count(),
                'revenue' => $revenue,
                'average' => $completed > 0 ? intdiv($revenue, $completed) : 0,
                'items' => $itemsSold,
            ],
            'days' => $days,
            'byPayment' => $this->groupedBy($from, $to, 'orders.payment_method'),
            'byType' => $this->groupedBy($from, $to, 'orders.order_type'),
            'byCategory' => $this->completedItems($from, $to)
                ->join('menus', 'menus.id', '=', 'order_items.menu_id')
                ->join('categories', 'categories.id', '=', 'menus.category_id')
                ->selectRaw('categories.name as label, SUM(order_items.quantity) as quantity, SUM('.self::LINE_TOTAL.') as revenue')
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('revenue')
                ->get(),
            'topMenus' => $this->completedItems($from, $to)
                ->join('menus', 'menus.id', '=', 'order_items.menu_id')
                ->selectRaw('menus.name as label, SUM(order_items.quantity) as quantity, SUM('.self::LINE_TOTAL.') as revenue')
                ->groupBy('menus.id', 'menus.name')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Orders of the range (every status) for the CSV export.
     *
     * @return Builder<Order>
     */
    public function exportQuery(CarbonInterface $from, CarbonInterface $to): Builder
    {
        return Order::with(['user', 'items'])
            ->whereBetween('ordered_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderBy('ordered_at')
            ->orderBy('id');
    }

    private function completedItems(CarbonInterface $from, CarbonInterface $to)
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.ordered_at', [$from, $to]);
    }

    /**
     * @return Collection<int, object>
     */
    private function groupedBy(CarbonInterface $from, CarbonInterface $to, string $column): Collection
    {
        return $this->completedItems($from, $to)
            ->selectRaw($column.' as label, COUNT(DISTINCT orders.id) as orders, SUM('.self::LINE_TOTAL.') as revenue')
            ->groupBy($column)
            ->orderByDesc('revenue')
            ->get();
    }
}
