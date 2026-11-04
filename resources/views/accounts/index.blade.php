<!-- Memanggil kerangka utama -->
@extends('layouts.app')

<!-- Mengisi bagian @yield('title') -->
@section('title', 'Daftar Akun Keuangan')

<!-- Mengisi bagian @yield('content') -->
@section('content')
@php
    $typeLabels = ['cash' => 'Cash', 'bank' => 'Bank', 'e-wallet' => 'E-Wallet'];
    $totalSaldo = $accounts->sum('balance');
    $terbesar   = $accounts->sortByDesc('balance')->first();
@endphp

<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Kelola Akun</h1>
            <p class="page-sub">Dompet, rekening bank, dan e-wallet Anda.</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="btn"><x-icon name="plus" size="18" /> Tambah Akun</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="status">
            <x-icon name="check-circle" size="20" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Ringkasan --}}
    <section class="stat-grid" aria-label="Ringkasan akun" style="margin-bottom: 20px;">
        <div class="stat">
            <span class="stat-icon is-green"><x-icon name="wallet" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Total Saldo</div>
                <div class="stat-value {{ $totalSaldo < 0 ? 'is-expense' : '' }}">
                    {{ $totalSaldo < 0 ? '-' : '' }}Rp {{ number_format(abs($totalSaldo), 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-blue"><x-icon name="landmark" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Jumlah Akun</div>
                <div class="stat-value">{{ $accounts->count() }} Akun</div>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon is-orange"><x-icon name="trending-up" size="22" /></span>
            <div class="stat-body">
                <div class="stat-label">Saldo Terbesar</div>
                <div class="stat-value">{{ $terbesar ? 'Rp ' . number_format($terbesar->balance, 0, ',', '.') : '-' }}</div>
                @if ($terbesar)
                    <div class="stat-sub">{{ $terbesar->name }}</div>
                @endif
            </div>
        </div>
    </section>

    {{-- Tabel --}}
    <section class="panel" aria-label="Daftar akun">
        @if ($accounts->isEmpty())
            <div class="empty">
                <span class="empty-icon"><x-icon name="wallet" size="26" /></span>
                <strong>Belum ada akun</strong>
                <span>Buat akun pertama Anda (cash, bank, atau e-wallet).</span>
                <a href="{{ route('accounts.create') }}" class="btn"><x-icon name="plus" size="18" /> Tambah Akun</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table table-accounts">
                    <thead>
                        <tr>
                            <th scope="col">Nama Akun</th>
                            <th scope="col">Tipe</th>
                            <th scope="col" class="t-right">Saldo</th>
                            <th scope="col" class="t-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            @php $slug = \Illuminate\Support\Str::slug($account->type); @endphp
                            <tr>
                                <td>
                                    <div class="cell-account">
                                        <span class="avatar type-{{ $slug }}" aria-hidden="true">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                                        <span class="cell-name">{{ $account->name }}</span>
                                    </div>
                                </td>
                                <td><span class="badge type-{{ $slug }}">{{ $typeLabels[$slug] ?? $account->type }}</span></td>
                                <td class="amount {{ $account->balance < 0 ? 'is-expense' : '' }}">
                                    {{ $account->balance < 0 ? '-' : '' }}Rp {{ number_format(abs($account->balance), 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('transactions.index', ['account_id' => $account->id]) }}" class="icon-btn"
                                           title="Lihat transaksi" aria-label="Lihat transaksi akun {{ $account->name }}">
                                            <x-icon name="list" size="18" />
                                        </a>
                                        <a href="{{ route('accounts.edit', $account->id) }}" class="icon-btn"
                                           title="Edit" aria-label="Edit akun {{ $account->name }}">
                                            <x-icon name="pencil" size="18" />
                                        </a>
                                        <form action="{{ route('accounts.destroy', $account->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus akun ini? Semua transaksi pada akun ini juga akan ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-btn is-danger"
                                                    title="Hapus" aria-label="Hapus akun {{ $account->name }}">
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
</div>
@endsection
