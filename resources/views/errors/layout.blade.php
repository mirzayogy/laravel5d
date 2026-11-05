@extends('layouts.app')

@section('title', $code)

@section('content')
    <x-empty-state :icon="$icon" :title="$code.' · '.$heading" :text="$text">
        <a href="{{ route('home') }}" class="btn btn-primary">Back to menu</a>
        <a href="javascript:history.back()" class="btn btn-ghost">Go back</a>
    </x-empty-state>
@endsection
