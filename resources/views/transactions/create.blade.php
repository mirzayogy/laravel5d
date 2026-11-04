@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Tambah Transaksi</h1>
            <p class="page-sub">Catat pemasukan atau pengeluaran baru.</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-ghost"><x-icon name="arrow-left" size="18" /> Kembali</a>
    </div>

    <form action="{{ route('transactions.store') }}" method="POST" class="panel form-card">
        @csrf
        @include('transactions._form', ['transaction' => null, 'submitLabel' => 'Simpan Transaksi'])
    </form>
</div>
@endsection
