<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        // Total saldo dari seluruh akun
        $totalBalance = Account::sum('balance');

        // Jumlah akun yang terdaftar
        $totalAccounts = Account::count();

        // Total seluruh transaksi
        $totalTransactions = Transaction::count();

        // Pemasukan & pengeluaran bulan ini
        $monthly = Transaction::whereBetween('transaction_date', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ]);

        $monthlyIncome  = (clone $monthly)->where('type', 'income')->sum('amount');
        $monthlyExpense = (clone $monthly)->where('type', 'expense')->sum('amount');

        // 5 transaksi terbaru
        $recentTransactions = Transaction::with(['account', 'category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Kirim semua data ke tampilan dashboard
        return view('dashboard', compact(
            'totalBalance',
            'totalAccounts',
            'totalTransactions',
            'monthlyIncome',
            'monthlyExpense',
            'recentTransactions'
        ));
    }
}
