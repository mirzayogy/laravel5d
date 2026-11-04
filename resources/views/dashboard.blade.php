@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    @foreach ($ringkasan as $item)
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $item['warna'] }}">
                <div class="inner">
                    <h3>{{ $item['jumlah'] }}</h3>
                    <p>{{ $item['nama'] }}</p>
                </div>
                <div class="icon"><i class="{{ $item['ikon'] }}"></i></div>
            </div>
        </div>
    @endforeach
</div>
@endsection