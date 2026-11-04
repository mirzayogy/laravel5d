@extends('layouts.app')

@section('title', 'Edit Akun')

@section('content')
<div class="container">
    <div class="page-head">
        <div>
            <h1 class="page-title">Edit Akun</h1>
            <p class="page-sub">Ubah data akun {{ $account->name }}.</p>
        </div>
        <a href="{{ route('accounts.index') }}" class="btn btn-ghost"><x-icon name="arrow-left" size="18" /> Kembali</a>
    </div>

    <form action="{{ route('accounts.update', $account->id) }}" method="POST" class="panel form-card">
        @csrf
        @method('PUT')
        @include('accounts._form', [
            'submitLabel'  => 'Perbarui Akun',
            'balanceLabel' => 'Saldo',
            'balanceHint'  => 'Mengubah saldo di sini tidak tercatat sebagai transaksi. Untuk koreksi yang tercatat, gunakan menu Transaksi.',
        ])
    </form>
</div>
@endsection
