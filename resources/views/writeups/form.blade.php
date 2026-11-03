@extends('layouts.app')

@section('title', $writeup ? 'Edit writeup' : 'New writeup')

@section('content')
    <x-page-header :title="$writeup ? 'Edit writeup' : 'New writeup'" :subtitle="'For '.$challenge->title" icon="edit"
                   :crumbs="['Writeups' => route('writeups.index'), ($writeup ? 'Edit' : 'New') => null]" />

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <form method="POST" action="{{ $writeup ? route('writeups.update', $writeup) : route('writeups.store', $challenge) }}"
                  x-data="mdPreview('{{ route('writeups.preview') }}')" class="space-y-4">
                @csrf
                @if ($writeup)
                    @method('PUT')
                @endif

                <x-input name="title" label="Title" :value="$writeup?->title" required placeholder="How we solved {{ $challenge->title }}" />

                <div>
                    <div class="mb-1 flex items-center justify-between">
                        <label for="content" class="text-sm font-semibold text-slate-700">Content (Markdown)</label>
                        <div class="flex rounded-full bg-sky-50 p-1 text-xs font-bold">
                            <button type="button" @click="tab = 'write'" :class="tab === 'write' ? 'bg-white text-sky-800 shadow-sm' : 'text-slate-500'" class="rounded-full px-3 py-1">Write</button>
                            <button type="button" @click="show($refs.content.value)" :class="tab === 'preview' ? 'bg-white text-sky-800 shadow-sm' : 'text-slate-500'" class="rounded-full px-3 py-1">Preview</button>
                        </div>
                    </div>
                    <div x-show="tab === 'write'">
                        <textarea id="content" name="content" x-ref="content" rows="16" required
                                  class="w-full rounded-2xl border-2 bg-white px-4 py-3 font-mono text-sm focus:outline-none {{ $errors->has('content') ? 'border-coral-300' : 'border-sky-100 focus:border-sky-400' }}">{{ old('content', $writeup?->content ?? "## Overview\n\nWhat was the challenge about, and what did we know at the start?\n\n## Steps\n\n1. \n2. \n3. \n\n## Takeaways\n\nWhat did we learn?") }}</textarea>
                        @error('content')<p class="mt-1 text-xs font-medium text-coral-700">{{ $message }}</p>@enderror
                    </div>
                    <div x-show="tab === 'preview'" x-cloak class="min-h-64 rounded-2xl border-2 border-sky-100 bg-white p-4">
                        <p x-show="loading" class="text-sm text-slate-500">Rendering...</p>
                        <div class="md" x-html="html"></div>
                    </div>
                </div>

                <x-select name="status" label="Visibility" hint="Drafts are only visible to you.">
                    @foreach (['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $writeup?->status ?? 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select>

                <div class="flex flex-wrap gap-2">
                    <x-button type="submit" icon="check">Save writeup</x-button>
                    <x-button :href="$writeup ? route('writeups.show', $writeup) : route('writeups.index')" variant="ghost">Cancel</x-button>
                </div>
            </form>
        </x-card>

        <div class="space-y-6">
            <x-card tone="sky">
                <h2 class="font-extrabold">Challenge</h2>
                <div class="mt-2 flex flex-wrap gap-1.5">
                    <x-category-badge :name="$challenge->category->name" />
                    <x-difficulty :level="$challenge->difficulty" />
                </div>
                <div class="mt-2 font-bold">{{ $challenge->title }}</div>
                <div class="text-xs text-slate-500">{{ $challenge->competition->title }}, {{ $challenge->points }} points</div>
            </x-card>
            <x-card>
                <h2 class="font-extrabold">Markdown tips</h2>
                <ul class="mt-2 space-y-1.5 text-sm text-slate-600">
                    <li><code class="rounded bg-sky-50 px-1 font-mono">## Heading</code> for sections</li>
                    <li><code class="rounded bg-sky-50 px-1 font-mono">**bold**</code> and <code class="rounded bg-sky-50 px-1 font-mono">`code`</code></li>
                    <li>Fence code with three backticks</li>
                    <li><code class="rounded bg-sky-50 px-1 font-mono">- item</code> for lists</li>
                </ul>
                <p class="mt-3 rounded-2xl bg-coral-50 px-3 py-2 text-xs text-coral-900">Please do not paste the flag itself, so other players can still enjoy the challenge.</p>
            </x-card>
        </div>
    </div>
@endsection
