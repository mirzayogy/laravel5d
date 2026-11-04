@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Ubah Transaksi</h1>
            <p class="page-sub">Saldo akun akan disesuaikan otomatis setelah disimpan.</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-ghost"><x-icon name="arrow-left" size="18" /> Kembali</a>
    </div>

    <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="panel form-card">
        @csrf
        @method('PUT')
        @include('transactions._form', ['submitLabel' => 'Perbarui Transaksi'])
    </form>
</div>
@endsection
