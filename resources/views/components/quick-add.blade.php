{{-- Quick add: a name and a category are enough, the target and unit follow from the category. --}}
@php
    $cats = \App\Models\Category::orderBy('name')->get();
    $options = $cats->map(fn ($c) => ['value' => $c->id, 'label' => trim($c->icon.' '.$c->display_name), 'dot' => $c->color])->values()->all();
    $examples = $cats->mapWithKeys(fn ($c) => [$c->id => __('e.g. :example', ['example' => __(config('habitude.defaults.'.$c->slug.'.example', 'Name your habit'))])]);
    $summary = $cats->mapWithKeys(fn ($c) => [$c->id => ($d = config('habitude.defaults.'.$c->slug)) ? __(':n a day', ['n' => $d['target'].' '.__($d['unit'])]) : '']);
@endphp

<dialog id="quick-add" aria-labelledby="quick-add-title"
    class="m-auto w-full max-w-md rounded-xl border border-mist/60 bg-paper p-0 text-left text-ink backdrop:bg-ink/40">
    <form action="{{ route('habits.store') }}" method="POST" data-quick-add data-examples="{{ json_encode($examples) }}" data-summary="{{ json_encode($summary) }}">
        @csrf
        <input type="hidden" name="quick" value="1">
        <input type="hidden" name="back" value="{{ request()->fullUrl() }}">
        <div class="space-y-4 p-6">
            <div>
                <h2 id="quick-add-title" class="text-subheading">{{ __('Add a habit') }}</h2>
                <p class="mt-1 text-body-sm text-pewter">{{ __('Just a name and a category. You can fine-tune it later.') }}</p>
            </div>
            <div>
                <label class="label">{{ __('Category') }}</label>
                <x-select name="category_id" :options="$options" placeholder="{{ __('Choose a category') }}" label="{{ __('Category') }}" />
                <p class="help" data-quick-summary>{{ __('We pick a sensible daily target for you.') }}</p>
            </div>
            <div>
                <label for="quick-name" class="label">{{ __('Name') }}</label>
                <input type="text" id="quick-name" name="name" maxlength="100" placeholder="{{ __('e.g. Drink water') }}" class="input" required>
            </div>
        </div>
        <div class="flex items-center justify-between gap-2 border-t border-mist/60 px-6 py-4">
            <a href="{{ route('habits.create') }}" class="text-body-sm text-pewter underline hover:text-ink">{{ __('More options') }}</a>
            <div class="flex gap-2">
                <button type="button" class="btn btn-outline" data-modal-close>{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-primary"><x-icon name="plus" /> {{ __('Add habit') }}</button>
            </div>
        </div>
    </form>
</dialog>
