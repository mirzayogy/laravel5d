@extends('layouts.app')

@section('title', $code)

@section('content')
    <x-empty-state :icon="$icon" :title="$code.' · '.__($heading)" :text="__($text)">
        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Back to menu') }}</a>
        <a href="javascript:history.back()" class="btn btn-ghost">{{ __('Go back') }}</a>
    </x-empty-state>
@endsection
