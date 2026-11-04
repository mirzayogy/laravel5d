<x-app-layout title="{{ __('Categories') }}" :crumbs="[[__('Admin'), route('admin.overview')], [__('Categories')]]">
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="text-subheading">{{ __('Categories') }} <span class="text-pewter font-normal">({{ $categories->count() }})</span></h1>
                <p class="text-body-sm text-pewter">{{ __('The themes your habits are grouped into.') }}</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <x-icon name="plus" /> {{ __('Add category') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Habits') }}</th>
                        <th class="w-28 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="inline-flex items-center gap-2 font-medium text-ink">
                                    <span class="size-3 rounded-md" style="background: {{ $category->color }}" aria-hidden="true"></span>
                                    {{ $category->icon }} {{ $category->display_name }}
                                </span>
                            </td>
                            <td><span class="badge">{{ $category->slug }}</span></td>
                            <td>{{ $category->habits_count }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline btn-icon" aria-label="{{ __('Edit :name', ['name' => $category->display_name]) }}">
                                        <x-icon name="pencil" />
                                    </a>
                                    <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-category-{{ $category->id }}" aria-label="{{ __('Delete :name', ['name' => $category->display_name]) }}">
                                        <x-icon name="trash" />
                                    </button>
                                </div>
                                <x-delete-modal id="delete-category-{{ $category->id }}"
                                    :action="route('admin.categories.destroy', $category)"
                                    title="{{ __('Delete category') }}"
                                    :message="__('Delete the category “:name”?', ['name' => $category->display_name])" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-pewter">{{ __('No categories yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
