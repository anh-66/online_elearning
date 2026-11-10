@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center gap-3 sm:gap-4 select-none">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-100 dark:border-neutral-800/80 text-gray-300 dark:text-neutral-700 cursor-not-allowed bg-gray-50/50 dark:bg-neutral-900/40 transition-colors" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-[#161b22] text-gray-500 dark:text-gray-400 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:border-[#019D91]/40 dark:hover:border-[#02b3a5]/40 hover:bg-gray-50 dark:hover:bg-neutral-800 transition-all shadow-sm" aria-label="{{ __('pagination.previous') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="flex items-center gap-1.5 sm:gap-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="w-8 h-10 flex items-center justify-center text-gray-300 dark:text-neutral-600 font-bold text-sm tracking-widest">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#019D91] text-white font-bold text-sm shadow-md shadow-[#019D91]/25">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-neutral-800 font-medium text-sm transition-all" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-[#161b22] text-gray-500 dark:text-gray-400 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:border-[#019D91]/40 dark:hover:border-[#02b3a5]/40 hover:bg-gray-50 dark:hover:bg-neutral-800 transition-all shadow-sm" aria-label="{{ __('pagination.next') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-gray-100 dark:border-neutral-800/80 text-gray-300 dark:text-neutral-700 cursor-not-allowed bg-gray-50/50 dark:bg-neutral-900/40 transition-colors" aria-disabled="true">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </nav>
@endif
