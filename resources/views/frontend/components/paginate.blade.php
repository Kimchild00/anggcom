{{--@if ($paginator->lastPage() > 1)--}}
{{--<div class="w-full flex justify-center text-sm text-gray-600">--}}
    {{--<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">--}}
        {{--@if ($paginator->currentPage() == 1)--}}
            {{--<span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">--}}
                {{--<span class="sr-only">Previous</span>--}}
                {{--<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
                  {{--<path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />--}}
                {{--</svg>--}}
            {{--</span>--}}
        {{--@else--}}
            {{--<a href="{{ $paginator->url(1) }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">--}}
                {{--<span class="sr-only">Previous</span>--}}
                {{--<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
                  {{--<path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />--}}
                {{--</svg>--}}
            {{--</a>--}}
        {{--@endif--}}

        {{--@for ($i = 1; $i <= $paginator->lastPage(); $i++)--}}
          {{--@if ($paginator->currentPage() == $i)--}}
              {{--<span class="z-10 bg-indigo-50 border border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2">--}}
                {{--{{ $i }}--}}
              {{--</span>--}}
            {{--@else--}}
                {{--<a--}}
                    {{--href="{{ $paginator->url($i) }}"--}}
                    {{--aria-current="page"--}}
                    {{--class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"--}}
                {{-->--}}
                    {{--{{ $i }}--}}
                {{--</a>--}}
            {{--@endif--}}
        {{--@endfor--}}

        {{--@if ($paginator->currentPage() == $paginator->lastPage())--}}
            {{--<span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">--}}
                {{--<span class="sr-only">Next</span>--}}
                    {{--<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
                    {{--<path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />--}}
                {{--</svg>--}}
            {{--</span>--}}
        {{--@else--}}
            {{--<a href="{{ $paginator->url($paginator->currentPage()+1) }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">--}}
                {{--<span class="sr-only">Next</span>--}}
                {{--<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
                    {{--<path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />--}}
                {{--</svg>--}}
            {{--</a>--}}
        {{--@endif--}}
    {{--</nav>--}}
{{--</div>--}}
{{--@endif--}}



@if ($paginator->lastPage() > 1)
    <div class="w-full flex justify-center text-sm text-gray-600">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <?php
            // config
        $link_limit = 15; // maximum number of links (a little bit inaccurate, but will be ok for now)
            ?>

            @if($paginator->currentPage() == 1)
                <span class="z-10 bg-indigo-50 border border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2">
                    First
                </span>
            @else
                <a
                        href="{{ $paginator->url(1) }}"
                        aria-current="page"
                        class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                >
                    First
                </a>
            @endif

            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                <?php
                $half_total_links = floor($link_limit / 2);
                $from = $paginator->currentPage() - $half_total_links;
                $to = $paginator->currentPage() + $half_total_links;
                if ($paginator->currentPage() < $half_total_links) {
                    $to += $half_total_links - $paginator->currentPage();
                }
                if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                    $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
                }
                ?>
                @if ($from < $i && $i < $to)
                    @if($paginator->currentPage() == $i)
                        <span class="z-10 bg-indigo-50 border border-indigo-500 text-white bg-indigo-500 relative inline-flex items-center px-4 py-2">
                            {{ $i }}
                        </span>
                    @elseif($i == $from + 1)
                        @if($i > 1)
                            <a
                                href="{{ $paginator->url($i) }}"
                                aria-current="page"
                                class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                            >
                            ...
                            </a>
                        @endif
                        <a
                            href="{{ $paginator->url($i) }}"
                            aria-current="page"
                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                        >
                            {{ $i }}
                        </a>
                    @elseif($i == ($to - 1))
                        <a
                            href="{{ $paginator->url($i) }}"
                            aria-current="page"
                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                        >
                            {{ $i }}
                        </a>
                        <a
                            href="{{ $paginator->url($i+1) }}"
                            aria-current="page"
                            class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                        >
                            ...
                        </a>
                    @else
                        <a
                                href="{{ $paginator->url($i) }}"
                                aria-current="page"
                                class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                        >
                            {{ $i }}
                        </a>
                    @endif
                @endif

            @endfor


            @if($paginator->currentPage() == $paginator->lastPage())
                <span class="z-10 bg-indigo-50 border border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2">
                End
            </span>
            @else
                <a
                        href="{{ $paginator->url($paginator->lastPage()) }}"
                        aria-current="page"
                        class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border"
                >
                    Last
                </a>
            @endif
        </nav>
    </div>
@endif