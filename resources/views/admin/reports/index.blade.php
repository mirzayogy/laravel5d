@extends('layouts.app')

@section('title', __('Reports'))

@section('content')
    @php
        $maxRevenue = max(1, (int) $report['days']->max('revenue'));
        $totals = $report['totals'];
        $labels = ['cash' => 'Cash', 'qris' => 'QRIS', 'transfer' => 'Bank transfer', 'ewallet' => 'E-wallet', 'dine_in' => 'Dine in', 'takeaway' => 'Takeaway'];
    @endphp

    <x-page-header :title="__('Sales reports')" :subtitle="__('Pendapatan dihitung dari pesanan berstatus completed.')">
        <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}" class="btn btn-primary">{{ __('Export CSV') }}</a>
    </x-page-header>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="rise mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label for="from" class="label">{{ __('From') }}</label>
            <input id="from" name="from" type="date" value="{{ $from }}" class="input">
        </div>
        <div>
            <label for="to" class="label">{{ __('To') }}</label>
            <input id="to" name="to" type="date" value="{{ $to }}" class="input">
        </div>
        <button type="submit" class="btn btn-ghost">{{ __('Apply') }}</button>
        <div class="flex flex-wrap gap-2 sm:ml-4">
            @foreach ([7 => __('7 days'), 30 => __('30 days'), 90 => __('90 days')] as $days => $label)
                <a href="{{ route('admin.reports.index', ['from' => now()->subDays($days - 1)->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}" class="chip">{{ $label }}</a>
            @endforeach
        </div>
    </form>

    <div class="rise grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Revenue') }}</div><div class="mt-1 text-2xl font-semibold">Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Orders') }}</div><div class="mt-1 text-2xl font-semibold">{{ $totals['orders'] }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Completed') }}</div><div class="mt-1 text-2xl font-semibold">{{ $totals['completed'] }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Average order') }}</div><div class="mt-1 text-2xl font-semibold">Rp {{ number_format($totals['average'], 0, ',', '.') }}</div></div>
        <div class="card p-5"><div class="text-sm text-stone-500">{{ __('Items sold') }}</div><div class="mt-1 text-2xl font-semibold">{{ $totals['items'] }}</div></div>
    </div>

    <section class="rise card mt-6 p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold">{{ __('Revenue per day') }}</h2>
            <span class="text-xs text-stone-500">{{ __(':count cancelled', ['count' => $totals['cancelled']]) }}</span>
        </div>
        @if ($totals['completed'] === 0)
            <x-empty-state class="mt-4 border-0 py-8" icon="📈" :title="__('No completed orders in this period')" :text="__('Choose another date range.')" />
        @else
            <div class="mt-5 flex h-48 items-end gap-0.5 overflow-x-auto">
                @foreach ($report['days'] as $day)
                    <div class="group flex h-full min-w-2 flex-1 flex-col justify-end" title="{{ $day['label'] }}: Rp {{ number_format($day['revenue'], 0, ',', '.') }} ({{ $day['orders'] }} orders)">
                        <div class="w-full rounded-t bg-brand-300 transition group-hover:bg-brand-600" style="height: {{ $day['revenue'] > 0 ? max(3, round($day['revenue'] / $maxRevenue * 100)) : 0 }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="mt-2 flex justify-between text-xs text-stone-500">
                <span>{{ $report['days']->first()['label'] }}</span>
                <span>{{ $report['days']->last()['label'] }}</span>
            </div>
        @endif
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        @foreach ([[__('By payment method'), $report['byPayment']], [__('By order type'), $report['byType']]] as [$title, $rows])
            <section class="rise card overflow-x-auto p-5">
                <h2 class="text-base font-semibold">{{ $title }}</h2>
                @if ($rows->isEmpty())
                    <p class="mt-3 text-sm text-stone-500">{{ __('No data for this period.') }}</p>
                @else
                    <table class="mt-3 min-w-full text-sm">
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($rows as $row)
                                <tr>
                                    <td class="py-2">{{ __($labels[$row->label] ?? ($row->label ?: '-')) }}</td>
                                    <td class="py-2 text-right text-stone-500">{{ __(':count orders', ['count' => $row->orders]) }}</td>
                                    <td class="py-2 text-right font-medium">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        @endforeach

        <section class="rise card overflow-x-auto p-5">
            <h2 class="text-base font-semibold">{{ __('By category') }}</h2>
            @if ($report['byCategory']->isEmpty())
                <p class="mt-3 text-sm text-stone-500">{{ __('No data for this period.') }}</p>
            @else
                <table class="mt-3 min-w-full text-sm">
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($report['byCategory'] as $row)
                            <tr>
                                <td class="py-2">{{ $row->label }}</td>
                                <td class="py-2 text-right text-stone-500">{{ __(':count items', ['count' => $row->quantity]) }}</td>
                                <td class="py-2 text-right font-medium">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        <section class="rise card overflow-x-auto p-5">
            <h2 class="text-base font-semibold">{{ __('Top 10 menus') }}</h2>
            @if ($report['topMenus']->isEmpty())
                <p class="mt-3 text-sm text-stone-500">{{ __('No data for this period.') }}</p>
            @else
                <table class="mt-3 min-w-full text-sm">
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($report['topMenus'] as $row)
                            <tr>
                                <td class="py-2"><span class="mr-2 text-stone-400">{{ $loop->iteration }}.</span>{{ $row->label }}</td>
                                <td class="py-2 text-right text-stone-500">{{ __(':count sold', ['count' => $row->quantity]) }}</td>
                                <td class="py-2 text-right font-medium">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection
