@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <x-page-header title="Notifications" :subtitle="$unread ? $unread.' unread' : 'You are all caught up.'" icon="bell">
        <x-slot:actions>
            @if ($unread)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <x-button type="submit" variant="ghost" icon="check">Mark all as read</x-button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            @php
                $tone = ['sky' => 'bg-sky-100 text-sky-700', 'coral' => 'bg-coral-100 text-coral-700', 'green' => 'bg-emerald-100 text-emerald-700', 'amber' => 'bg-amber-100 text-amber-700'][$notification->data['tone'] ?? 'sky'] ?? 'bg-sky-100 text-sky-700';
            @endphp
            <x-card class="flex items-center gap-4 {{ $notification->read_at ? '' : 'ring-2 ring-coral-200' }}">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl {{ $tone }}"><x-icon :name="$notification->data['icon'] ?? 'bell'" class="size-5" /></span>
                <div class="min-w-0 grow">
                    <div class="flex items-center gap-2 font-bold">
                        {{ $notification->data['title'] ?? 'Notification' }}
                        @unless ($notification->read_at)<span class="size-2 rounded-full bg-coral-500"></span>@endunless
                    </div>
                    <div class="text-sm text-slate-600">{{ $notification->data['body'] ?? '' }}</div>
                    <div class="text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <x-button type="submit" size="sm" variant="soft" icon="arrow-right">Open</x-button>
                </form>
            </x-card>
        @empty
            <x-empty icon="bell" title="No notifications yet" text="Team news, first bloods, and role changes show up here." />
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links('pagination.custom') }}</div>
@endsection
