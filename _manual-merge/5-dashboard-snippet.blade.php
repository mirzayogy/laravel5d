{{-- Ganti snippet dashboard lama dengan ini (dashboard.blade.php).
     Pastikan DashboardController dari paket sebelumnya sudah dipasang. --}}
<link rel="stylesheet" href="{{ asset('css/transactions.css') }}">

@php $netMonth = $monthlyIncome - $monthlyExpense; @endphp

<div class="trx" style="margin-top: 24px;">
    <div class="trx-cards">
        <div class="trx-card income">
            <div class="trx-card-label"><span class="trx-dot">&uarr;</span> Pemasukan Bulan Ini</div>
            <div class="trx-card-value">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
        </div>
        <div class="trx-card expense">
            <div class="trx-card-label"><span class="trx-dot">&darr;</span> Pengeluaran Bulan Ini</div>
            <div class="trx-card-value">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
        </div>
        <div class="trx-card net">
            <div class="trx-card-label"><span class="trx-dot">=</span> Selisih Bulan Ini</div>
            <div class="trx-card-value {{ $netMonth < 0 ? 'is-neg' : '' }}">
                {{ $netMonth < 0 ? '-' : '' }}Rp {{ number_format(abs($netMonth), 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="trx-header" style="margin-bottom: 12px;">
        <div class="trx-title"><h1 style="font-size: 20px;">Transaksi Terbaru</h1></div>
        <a href="{{ route('transactions.index') }}" class="trx-btn trx-btn-ghost">Lihat semua &rarr;</a>
    </div>

    <div class="trx-table-wrap">
        <table class="trx-table">
            <tbody>
                @forelse ($recentTransactions as $trx)
                    <tr>
                        <td class="trx-date">{{ $trx->transaction_date->translatedFormat('d M') }}</td>
                        <td><span class="trx-badge {{ $trx->type }}">{{ $trx->category->name }}</span></td>
                        <td class="trx-account">{{ $trx->account->name }}</td>
                        <td class="num trx-amount {{ $trx->type }}">
                            {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr><td><div class="trx-empty"><strong>Belum ada transaksi</strong></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
