@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Navigasi halaman">
        <span class="pager-info">
            Menampilkan {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }} transaksi
        </span>

        <ul class="pager-list">
            @if ($paginator->onFirstPage())
                <li><span class="pager-item is-disabled" aria-disabled="true"><x-icon name="chevron-left" size="18" /></span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pager-item" aria-label="Halaman sebelumnya"><x-icon name="chevron-left" size="18" /></a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pager-item is-disabled">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="pager-item is-active" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="pager-item" aria-label="Ke halaman {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pager-item" aria-label="Halaman berikutnya"><x-icon name="chevron-right" size="18" /></a></li>
            @else
                <li><span class="pager-item is-disabled" aria-disabled="true"><x-icon name="chevron-right" size="18" /></span></li>
            @endif
        </ul>
    </nav>
@endif
