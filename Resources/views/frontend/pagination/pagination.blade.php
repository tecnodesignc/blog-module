@if ($paginator->hasPages())
    <ul>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled" aria-disabled="true" aria-current="page"
                aria-label="@lang('pagination.previous')">
            </li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" aria-label="@lang('pagination.previous')"><i class="fas fa-angle-left"></i></a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li aria-current="page"><span class="page-numbers current">{{ $page }}</span>
                        </li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a class="" href="{{ $paginator->nextPageUrl() }}"
                   aria-label="@lang('pagination.next')"><i class="fas fa-angle-right"></i>
                </a>
            </li>
        @endif
    </ul>
@endif
