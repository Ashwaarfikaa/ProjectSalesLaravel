@if ($paginator->hasPages())
    @php
        // ->links() Laravel selalu mengirim $elements; fallback ini untuk pemanggilan manual.
        if (! isset($elements) || $elements === null) {
            $window = \Illuminate\Pagination\UrlWindow::make($paginator);
            $elements = array_filter([
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ]);
        }
    @endphp
    <div class="pagination" role="navigation" aria-label="Navigasi halaman">
        <span class="page-info">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </span>

        <ul>
            {{-- Halaman sebelumnya --}}
            <li class="{{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true">&laquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya">&laquo;</a>
                @endif
            </li>

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="{{ $page == $paginator->currentPage() ? 'active' : '' }}">
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Halaman berikutnya --}}
            <li class="{{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya">&raquo;</a>
                @else
                    <span aria-disabled="true">&raquo;</span>
                @endif
            </li>
        </ul>
    </div>
@endif
