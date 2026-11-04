@extends('layouts.app')

@section('content')
@php $net = $totalIncome - $totalExpense; @endphp

<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Transaksi</h1>
            <p class="page-sub">Riwayat pemasukan dan pengeluaran Anda.</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="btn"><x-icon name="plus" size="18" /> Tambah Transaksi</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">
            <x-icon name="check-circle" size="20" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Ringkasan (mengikuti filter yang aktif) --}}
    <section class="stat-grid" aria-label="Ringkasan sesuai filter" style="margin-bottom: 20px;">
        <div class="stat">
            <span class="stat-icon is-green"><x-icon name="arrow-up-circle" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Pemasukan</div>
                <div class="stat-value is-income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-red"><x-icon name="arrow-down-circle" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Pengeluaran</div>
                <div class="stat-value is-expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-slate"><x-icon name="equal" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Selisih</div>
                <div class="stat-value {{ $net < 0 ? 'is-expense' : '' }}">
                    {{ $net < 0 ? '-' : '' }}Rp {{ number_format(abs($net), 0, ',', '.') }}
                </div>
            </div>
        </div>
    </section>

    {{-- Filter --}}
    <form method="GET" action="{{ route('transactions.index') }}" class="panel filters" aria-label="Filter transaksi">
        <div class="field">
            <label class="label" for="f-month">Bulan</label>
            <input class="input" type="month" id="f-month" name="month" value="{{ request('month') }}">
        </div>
        <div class="field">
            <label class="label" for="f-type">Tipe</label>
            <select class="input" id="f-type" name="type">
                <option value="">Semua</option>
                <option value="income" @selected(request('type') === 'income')>Pemasukan</option>
                <option value="expense" @selected(request('type') === 'expense')>Pengeluaran</option>
            </select>
        </div>
        <div class="field">
            <label class="label" for="f-account">Akun</label>
            <select class="input" id="f-account" name="account_id">
                <option value="">Semua</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" @selected((string) request('account_id') === (string) $account->id)>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label class="label" for="f-category">Kategori</label>
            <select class="input" id="f-category" name="category_id">
                <option value="">Semua</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filters-actions">
            <button type="submit" class="btn">Filter</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>

    {{-- Tabel --}}
    <section class="panel" aria-label="Daftar transaksi">
        @if ($transactions->isEmpty())
            <div class="empty">
                <span class="empty-icon"><x-icon name="inbox" size="26" /></span>
                <strong>Belum ada transaksi</strong>
                <span>Coba ubah filter, atau catat transaksi pertama Anda.</span>
                <a href="{{ route('transactions.create') }}" class="btn"><x-icon name="plus" size="18" /> Tambah Transaksi</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Akun</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="t-right">Jumlah</th>
                            <th scope="col" class="t-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $trx)
                            <tr>
                                <td class="t-date">{{ $trx->transaction_date->translatedFormat('d M Y') }}</td>
                                <td><span class="badge {{ $trx->type }}">{{ $trx->category->name }}</span></td>
                                <td class="t-muted">{{ $trx->account->name }}</td>
                                <td><div class="t-trunc" title="{{ $trx->description }}">{{ $trx->description ?: '-' }}</div></td>
                                <td class="amount is-{{ $trx->type }}">
                                    {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('transactions.edit', $trx) }}" class="icon-btn"
                                           title="Edit" aria-label="Edit transaksi {{ $trx->category->name }} tanggal {{ $trx->transaction_date->translatedFormat('d M Y') }}">
                                            <x-icon name="pencil" size="18" />
                                        </a>
                                        <form action="{{ route('transactions.destroy', $trx) }}" method="POST"
                                              onsubmit="return confirm('Hapus transaksi ini? Saldo akun akan disesuaikan kembali.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn is-danger"
                                                    title="Hapus" aria-label="Hapus transaksi {{ $trx->category->name }} tanggal {{ $trx->transaction_date->translatedFormat('d M Y') }}">
                                                <x-icon name="trash" size="18" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{ $transactions->links('transactions.pagination') }}
</div>
@endsection
