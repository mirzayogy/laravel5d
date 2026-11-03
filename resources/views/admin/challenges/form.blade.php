@extends('layouts.app')

@section('title', $challenge ? 'Edit challenge' : 'New challenge')

@section('content')
    <x-page-header :title="$challenge ? 'Edit challenge' : 'New challenge'" icon="target"
                   :crumbs="['Admin' => route('admin.dashboard'), 'Challenges' => route('admin.challenges.index'), ($challenge ? 'Edit' : 'New') => null]" />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <form method="POST" action="{{ $challenge ? route('admin.challenges.update', $challenge) : route('admin.challenges.store') }}" class="space-y-4">
                    @csrf
                    @if ($challenge)
                        @method('PUT')
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-select name="competition_id" label="Competition" required>
                            @foreach ($competitions as $competition)
                                <option value="{{ $competition->id }}" @selected((int) old('competition_id', $challenge?->competition_id ?? request('competition')) === $competition->id)>{{ $competition->title }}</option>
                            @endforeach
                        </x-select>
                        <x-select name="category_id" label="Category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('category_id', $challenge?->category_id) === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input name="title" label="Title" :value="$challenge?->title" required />
                    <x-textarea name="description" label="Description (Markdown)" :value="$challenge?->description" rows="7" required hint="Use Markdown for code blocks, links, and lists." />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-input name="points" type="number" min="1" label="Points" :value="$challenge?->points ?? 100" required />
                        <x-select name="difficulty" label="Difficulty">
                            @foreach (['easy', 'medium', 'hard'] as $level)
                                <option value="{{ $level }}" @selected(old('difficulty', $challenge?->difficulty ?? 'easy') === $level)>{{ ucfirst($level) }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input name="flag" label="Flag{{ $challenge ? ' (leave empty to keep the current flag)' : '' }}" autocomplete="off" class="font-mono" :required="! $challenge" hint="The flag is stored as a hash and can never be shown again." />

                    <div>
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Tags</span>
                        @php($selectedTags = old('tag_ids', $challenge?->tags->pluck('id')->all() ?? []))
                        <div class="flex flex-wrap gap-2">
                            @forelse ($tags as $tag)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" class="peer sr-only" @checked(in_array($tag->id, $selectedTags))>
                                    <span class="inline-block rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm font-semibold text-slate-600 peer-checked:border-sky-500 peer-checked:bg-sky-500 peer-checked:text-white peer-focus-visible:outline-3 peer-focus-visible:outline-sky-400">#{{ $tag->name }}</span>
                                </label>
                            @empty
                                <span class="text-sm text-slate-500">No tag yet.</span>
                            @endforelse
                        </div>
                        <input name="new_tags" type="text" value="{{ old('new_tags') }}" placeholder="New tags, separated by commas" class="mt-3 w-full rounded-2xl border-2 border-sky-100 bg-white px-4 py-2.5 text-sm focus:border-sky-400 focus:outline-none">
                    </div>

                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_published" value="1" class="size-4 rounded border-sky-300 text-sky-500" @checked(old('is_published', $challenge?->is_published))>
                        Published (players can see it once the competition starts)
                    </label>

                    <div class="flex flex-wrap gap-2">
                        <x-button type="submit" icon="check">Save challenge</x-button>
                        <x-button :href="route('admin.challenges.index')" variant="ghost">Back</x-button>
                    </div>
                </form>
            </x-card>

            @if ($challenge)
                <x-card>
                    <h2 class="mb-3 flex items-center gap-2 text-lg font-extrabold tracking-tight"><x-icon name="bulb" class="size-5 text-amber-500" /> Hints</h2>
                    <div class="space-y-2">
                        @forelse ($challenge->hints as $hint)
                            <div class="flex items-start justify-between gap-3 rounded-2xl border-2 border-sky-100 p-3 text-sm">
                                <div>
                                    <p class="whitespace-pre-line">{{ $hint->content }}</p>
                                    <p class="mt-1 text-xs font-semibold text-amber-700">Costs {{ $hint->cost }} points</p>
                                </div>
                                <form method="POST" action="{{ route('admin.hints.destroy', $hint) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" size="sm" variant="danger" icon="trash">Delete</x-button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No hint yet.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('admin.hints.store', $challenge) }}" class="mt-4 space-y-3 border-t border-sky-100 pt-4">
                        @csrf
                        <x-textarea name="content" label="New hint" rows="2" required />
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="w-36"><x-input name="cost" type="number" min="0" label="Cost (points)" value="10" required /></div>
                            <x-button type="submit" variant="ghost" icon="plus">Add hint</x-button>
                        </div>
                    </form>
                </x-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-card tone="sky">
                <h2 class="font-extrabold">Writing tips</h2>
                <ul class="mt-2 space-y-2 text-sm text-slate-700">
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 text-emerald-600" /> Tell players what they get, not how to solve it.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 text-emerald-600" /> Easy challenges 100 points, hard ones 300 or more.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 text-emerald-600" /> Add two hints: a cheap nudge and a bigger one.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 size-4 text-emerald-600" /> Use the format <code class="rounded bg-white px-1 font-mono">FLAG{...}</code>.</li>
                </ul>
            </x-card>
            @if ($challenge)
                <x-card>
                    <h2 class="font-extrabold">Preview</h2>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <x-category-badge :name="$challenge->category->name" />
                        <x-difficulty :level="$challenge->difficulty" />
                    </div>
                    <x-button :href="route('challenges.show', $challenge)" size="sm" variant="soft" class="mt-3" icon="eye">Open as a player</x-button>
                </x-card>
            @endif
        </div>
    </div>
@endsection
