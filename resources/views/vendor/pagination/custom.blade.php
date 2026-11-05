@if ($paginator->hasPages())
    <div class="pagination">
        <div class="pagination-info">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </div>
        <div class="pagination-links">
            @if ($paginator->onFirstPage())
                <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span>{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="active"><span>{{ $page }}</span></span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
            @else
                <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
@endif
