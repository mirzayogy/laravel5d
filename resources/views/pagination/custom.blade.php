@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-xs font-medium text-slate-500">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>

        <div class="flex flex-wrap items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm text-slate-300">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm font-semibold text-sky-800 hover:border-sky-300">Prev</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-slate-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="rounded-full bg-sky-500 px-3 py-1 text-sm font-bold text-white shadow-[0_2px_0_0_var(--color-sky-700)]">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm font-semibold text-slate-700 hover:border-sky-300">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm font-semibold text-sky-800 hover:border-sky-300">Next</a>
            @else
                <span class="rounded-full border-2 border-sky-100 bg-white px-3 py-1 text-sm text-slate-300">Next</span>
            @endif
        </div>
    </nav>
@endif
