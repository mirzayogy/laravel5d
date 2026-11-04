@php
    $roleOptions = [['value' => 'user', 'label' => __('User'), 'tag' => __('sees only their own data')], ['value' => 'admin', 'label' => __('Admin'), 'tag' => __('full system access')]];
    $self = $user->is(auth()->user());
@endphp

<x-app-layout title="{{ __('Edit user') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Users'), route('admin.users.index')], [__('Edit')]]">
    <div class="grid max-w-5xl gap-6 lg:grid-cols-5">
        <div class="card lg:col-span-3">
            <div class="card-header">
                <h1 class="text-subheading">{{ __('Edit user') }}</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> {{ __('Back') }}</a>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="card-body space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="label">{{ __('Name') }}</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input @error('name') input-error @enderror" required>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="label">{{ __('Email') }}</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input @error('email') input-error @enderror" required>
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">{{ __('Role') }}</label>
                    <x-select name="role" :options="$roleOptions" :value="old('role', $user->role)" label="{{ __('Role') }}" />
                    @if ($self) <p class="help">{{ __('You cannot change your own role.') }}</p> @endif
                    @error('role') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="label">{{ __('Set a new password') }} <span class="font-normal text-pewter">{{ __('(optional)') }}</span></label>
                    <input type="password" id="password" name="password" autocomplete="new-password" class="input @error('password') input-error @enderror">
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary"><x-icon name="save" /> {{ __('Save changes') }}</button>
                </div>
            </form>
        </div>

        <aside class="card h-fit lg:col-span-2">
            <div class="card-header"><h2 class="text-subheading">{{ __('Activity') }}</h2><x-avatar :user="$user" size="size-10" /></div>
            <dl class="card-body space-y-2 text-body-sm">
                <div class="flex justify-between"><dt class="text-pewter">{{ __('Joined') }}</dt><dd class="font-medium">{{ $user->created_at->translatedFormat('j M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">{{ __('Habits') }}</dt><dd class="font-medium">{{ $user->habits_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">{{ __('Mood entries') }}</dt><dd class="font-medium">{{ $user->mood_entries_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">{{ __('Journal entries') }}</dt><dd class="font-medium">{{ $user->journal_entries_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">{{ __('Achievements') }}</dt><dd class="font-medium">{{ $user->achievements_count }}</dd></div>
            </dl>
        </aside>
    </div>
</x-app-layout>
