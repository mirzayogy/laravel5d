<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $start = now()->startOfMonth()->toDateString();
        $end   = now()->endOfMonth()->toDateString();

        $monthly = Transaction::whereBetween('transaction_date', [$start, $end]);

        // PENTING: samakan nama key 'totalBalance' & 'accountCount'
        // dengan variabel yang sudah dipakai di dashboard.blade.php Anda.
        return view('dashboard', [
            'totalBalance'       => Account::sum('balance'),
            'accountCount'       => Account::count(),
            'monthlyIncome'      => (clone $monthly)->where('type', 'income')->sum('amount'),
            'monthlyExpense'     => (clone $monthly)->where('type', 'expense')->sum('amount'),
            'recentTransactions' => Transaction::with(['account', 'category'])
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
