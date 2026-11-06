<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(Request $request): View
    {
        [$from, $to] = $this->range($request);

        $report = $this->reports->build($from, $to);

        return view('admin.reports.index', [
            'report' => $report,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);

        $filename = 'orders-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel opens it correctly

            fputcsv($out, ['Order', 'Date', 'Customer', 'Type', 'Table', 'Payment method', 'Payment status', 'Status', 'Items', 'Total (Rp)']);

            $this->reports->exportQuery($from, $to)->chunk(500, function ($orders) use ($out) {
                /** @var Order $order */
                foreach ($orders as $order) {
                    fputcsv($out, [
                        $order->order_number,
                        $order->ordered_at->format('Y-m-d H:i'),
                        $order->user->name,
                        $order->order_type,
                        $order->table_number,
                        $order->payment_method,
                        $order->payment_status,
                        $order->status,
                        $order->items->sum('quantity'),
                        $order->total(),
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function range(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $to = isset($data['to']) ? Carbon::parse($data['to']) : now();
        $from = isset($data['from']) ? Carbon::parse($data['from']) : $to->copy()->subDays(29);

        // keep the chart readable
        if ($from->diffInDays($to) > 120) {
            $from = $to->copy()->subDays(120);
        }

        return [$from, $to];
    }
}
