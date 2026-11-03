@extends('layouts.bare')

@section('title', 'Something broke')

@section('content')
    <div class="w-full max-w-md text-center">
        <div class="mx-auto flex size-24 animate-float items-center justify-center rounded-[2rem] bg-coral-500 text-white shadow-[0_6px_0_0_var(--color-coral-700)]">
            <x-icon name="flag" class="size-12" />
        </div>
        <p class="mt-8 text-6xl font-extrabold tracking-tight text-sky-500">500</p>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Something broke</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $exception->getMessage() ?: 'Something went wrong on our side. Please try again in a moment.' }}</p>
        <div class="mt-6 flex flex-wrap justify-center gap-2">
            <x-button href="/" icon="home">Back to home</x-button>
            <x-button href="javascript:history.back()" variant="ghost" icon="arrow-left">Go back</x-button>
        </div>
    </div>
@endsection
