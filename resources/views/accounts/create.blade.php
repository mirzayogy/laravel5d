@extends('layouts.app')

@section('title', 'Tambah Akun')

@section('content')
<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Tambah Akun Baru</h1>
            <p class="page-sub">Buat dompet, rekening, atau e-wallet baru.</p>
        </div>
        <a href="{{ route('accounts.index') }}" class="btn btn-ghost"><x-icon name="arrow-left" size="18" /> Kembali</a>
    </div>

    <form action="{{ route('accounts.store') }}" method="POST" class="panel form-card">
        @csrf
        @include('accounts._form', [
            'account'      => null,
            'submitLabel'  => 'Simpan Akun',
            'balanceLabel' => 'Saldo Awal',
            'balanceHint'  => 'Saldo akun sebelum transaksi dicatat.',
        ])
    </form>
</div>
@endsection
