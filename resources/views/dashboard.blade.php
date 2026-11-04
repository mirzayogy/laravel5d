@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content')
@php
    $netMonth = $monthlyIncome - $monthlyExpense;
    $maxFlow  = max($monthlyIncome, $monthlyExpense, 1);
    $incPct   = round($monthlyIncome / $maxFlow * 100);
    $expPct   = round($monthlyExpense / $maxFlow * 100);
@endphp

<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-sub">Selamat datang di UangKu, Aplikasi Pencatat Keuangan Pribadi Anda.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="btn"><x-icon name="plus" size="18" /> Tambah Transaksi</a>
    </div>

    {{-- Ringkasan utama --}}
    <section class="stat-grid" aria-label="Ringkasan utama">
        <div class="stat">
            <span class="stat-icon is-green"><x-icon name="wallet" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Total Saldo Aktif</div>
                <div class="stat-value">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-blue"><x-icon name="landmark" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Jumlah Akun/Dompet</div>
                <div class="stat-value">{{ $totalAccounts }} Akun</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-orange"><x-icon name="arrow-left-right" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">{{ $totalTransactions }}</div>
                <div class="stat-sub">transaksi tercatat</div>
            </div>
        </div>
    </section>

    {{-- Ringkasan bulan ini --}}
    <div class="section-head">
        <h2 class="section-title">Ringkasan {{ now()->translatedFormat('F Y') }}</h2>
    </div>

    <section class="stat-grid" aria-label="Ringkasan bulan ini">
        <div class="stat">
            <span class="stat-icon is-green"><x-icon name="arrow-up-circle" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Pemasukan Bulan Ini</div>
                <div class="stat-value is-income">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-red"><x-icon name="arrow-down-circle" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Pengeluaran Bulan Ini</div>
                <div class="stat-value is-expense">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-slate"><x-icon name="equal" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Selisih Bulan Ini</div>
                <div class="stat-value {{ $netMonth < 0 ? 'is-expense' : '' }}">
                    {{ $netMonth < 0 ? '-' : '' }}Rp {{ number_format(abs($netMonth), 0, ',', '.') }}
                </div>
                <div class="stat-sub">{{ $netMonth < 0 ? 'Pengeluaran melebihi pemasukan' : 'Pemasukan lebih besar' }}</div>
            </div>
        </div>
    </section>

    <div class="dash-grid">
        {{-- Arus kas --}}
        <section class="panel" aria-labelledby="flow-title">
            <div class="panel-head">
                <h2 class="panel-title" id="flow-title">Arus Kas Bulan Ini</h2>
            </div>
            <div class="panel-body">
                <div class="flow">
                    <div>
                        <div class="flow-meta">
                            <span>Pemasukan</span>
                            <span class="amount is-income">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</span>
                        </div>
                        <div class="bar" role="img" aria-label="Pemasukan {{ $incPct }} persen dari nilai terbesar bulan ini">
                            <span class="bar-fill is-income" style="width: {{ $incPct }}%"></span>
                        </div>
                    </div>
                    <div>
                        <div class="flow-meta">
                            <span>Pengeluaran</span>
                            <span class="amount is-expense">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</span>
                        </div>
                        <div class="bar" role="img" aria-label="Pengeluaran {{ $expPct }} persen dari nilai terbesar bulan ini">
                            <span class="bar-fill is-expense" style="width: {{ $expPct }}%"></span>
                        </div>
                    </div>
                </div>
                <p class="flow-note">Panjang batang dibandingkan terhadap nilai terbesar di antara keduanya.</p>
            </div>
        </section>

        {{-- Transaksi terbaru --}}
        <section class="panel" aria-labelledby="recent-title">
            <div class="panel-head">
                <h2 class="panel-title" id="recent-title">Transaksi Terbaru</h2>
                <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Lihat semua</a>
            </div>

            @if ($recentTransactions->isEmpty())
                <div class="empty">
                    <span class="empty-icon"><x-icon name="inbox" size="26" /></span>
                    <strong>Belum ada transaksi</strong>
                    <span>Catat pemasukan atau pengeluaran pertama Anda.</span>
                    <a href="{{ route('transactions.create') }}" class="btn"><x-icon name="plus" size="18" /> Tambah Transaksi</a>
                </div>
            @else
                <div class="table-wrap">
                    <table class="table table-compact">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Akun</th>
                                <th scope="col" class="t-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTransactions as $trx)
                                <tr>
                                    <td class="t-date">{{ $trx->transaction_date->translatedFormat('d M') }}</td>
                                    <td><span class="badge {{ $trx->type }}">{{ $trx->category->name }}</span></td>
                                    <td class="t-muted">{{ $trx->account->name }}</td>
                                    <td class="amount is-{{ $trx->type }}">
                                        {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
