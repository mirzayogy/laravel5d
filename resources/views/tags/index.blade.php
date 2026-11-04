<x-app-layout title="{{ __('Tags') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Tags')]]">
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="text-subheading">{{ __('Tags') }} <span class="text-pewter font-normal">({{ $tags->count() }})</span></h1>
                <p class="text-body-sm text-pewter">{{ __('Free-form labels you can attach to any habit.') }}</p>
            </div>
            <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
                <x-icon name="plus" /> {{ __('Add tag') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>{{ __('Tag') }}</th>
                        <th>{{ __('Habits') }}</th>
                        <th class="w-28 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge">{{ $tag->name }}</span></td>
                            <td>{{ $tag->habits_count }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-outline btn-icon" aria-label="{{ __('Edit :name', ['name' => $tag->name]) }}">
                                        <x-icon name="pencil" />
                                    </a>
                                    <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-tag-{{ $tag->id }}" aria-label="{{ __('Delete :name', ['name' => $tag->name]) }}">
                                        <x-icon name="trash" />
                                    </button>
                                </div>
                                <x-delete-modal id="delete-tag-{{ $tag->id }}"
                                    :action="route('admin.tags.destroy', $tag)"
                                    title="{{ __('Delete tag') }}"
                                    :message="__('Delete the tag “:name”? It will be removed from all habits.', ['name' => $tag->name])" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-pewter">{{ __('No tags yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
