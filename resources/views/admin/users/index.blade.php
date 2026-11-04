@php
    $roleOptions = [['value' => '', 'label' => __('All roles')], ['value' => 'user', 'label' => __('Users')], ['value' => 'admin', 'label' => __('Admins')]];
@endphp

<x-app-layout title="{{ __('Users') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Users')]]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">{{ __('Users') }} <span class="font-normal text-pewter">({{ $users->total() }})</span></h1>
            <p class="text-body-sm text-pewter">{{ __('Everyone who signed up. Deleting a user removes all of their data.') }}</p>
        </div>
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <label class="relative">
                <span class="sr-only">{{ __('Search users') }}</span>
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-steel" />
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Search name or email') }}" class="input w-64 pl-9">
            </label>
            <div class="w-36"><x-select name="role" :options="$roleOptions" :value="$role" label="{{ __('Filter by role') }}" :compact="true" :autosubmit="true" /></div>
            <button type="submit" class="btn btn-outline">{{ __('Search') }}</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th><th>{{ __('Role') }}</th><th>{{ __('Habits') }}</th><th>{{ __('Moods') }}</th><th>{{ __('Journals') }}</th><th>{{ __('Joined') }}</th><th class="w-28 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>
                                <span class="flex items-center gap-3">
                                    <x-avatar :user="$u" size="size-9" />
                                    <span class="min-w-0"><span class="block truncate font-medium text-ink">{{ $u->name }} @if ($u->is(auth()->user())) <span class="font-normal text-pewter">{{ __('(you)') }}</span> @endif</span><span class="block truncate text-caption text-pewter">{{ $u->email }}</span></span>
                                </span>
                            </td>
                            <td><span class="badge {{ $u->isAdmin() ? 'badge-active' : '' }}">{{ __(ucfirst($u->role)) }}</span></td>
                            <td>{{ $u->habits_count }}</td>
                            <td>{{ $u->mood_entries_count }}</td>
                            <td>{{ $u->journal_entries_count }}</td>
                            <td class="whitespace-nowrap">{{ $u->created_at->translatedFormat('j M Y') }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-outline btn-icon" aria-label="{{ __('Edit :name', ['name' => $u->name]) }}"><x-icon name="pencil" /></a>
                                    @unless ($u->is(auth()->user()))
                                        <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-user-{{ $u->id }}" aria-label="{{ __('Delete :name', ['name' => $u->name]) }}"><x-icon name="trash" /></button>
                                    @endunless
                                </div>
                                @unless ($u->is(auth()->user()))
                                    <x-delete-modal id="delete-user-{{ $u->id }}" :action="route('admin.users.destroy', $u)" title="{{ __('Delete user') }}"
                                        :message="__('Delete :name and all of their habits, check-ins, moods and journal entries?', ['name' => $u->name])" />
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-pewter">{{ __('No users match your search.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-mist/60 px-6 py-3">{{ $users->links() }}</div>
        @endif
    </div>
</x-app-layout>
