@if ($paginator->hasPages())
    <nav role="navigation"
         aria-label="{{ __('pagination.navigation') ?? 'Pagination' }}"
         data-slot="pagination"
         class="mx-auto flex w-full justify-center select-none"
         x-data="{
             hover: { left: '0px', top: '0px', width: '0px', height: '0px', opacity: 0 },
             setHover(el) {
                 if (!el) return;
                 this.hover = {
                     left: el.offsetLeft + 'px',
                     top: el.offsetTop + 'px',
                     width: el.offsetWidth + 'px',
                     height: el.offsetHeight + 'px',
                     opacity: 1
                 };
             },
             clearHover() {
                 this.hover.opacity = 0;
             }
         }">

        <ul data-slot="pagination-content"
            class="relative flex items-center gap-1 p-1 rounded-xl"
            @mouseleave="clearHover()">

            {{-- Smooth Gliding Hover Pill (Animate UI Fluid Motion) --}}
            <div class="absolute rounded-lg bg-black/[0.06] dark:bg-white/15 pointer-events-none transition-all duration-250 ease-[cubic-bezier(0.22,1,0.36,1)] z-0"
                 :style="`left: ${hover.left}; top: ${hover.top}; width: ${hover.width}; height: ${hover.height}; opacity: ${hover.opacity};`"
                 x-cloak>
            </div>

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li data-slot="pagination-item">
                    <span aria-disabled="true"
                          data-slot="pagination-link"
                          class="h-9 pl-2 pr-3 gap-1.5 text-sm font-medium rounded-lg inline-flex items-center justify-center text-neutral-300 dark:text-neutral-600 cursor-not-allowed select-none opacity-50">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline">Trước</span>
                    </span>
                </li>
            @else
                <li data-slot="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}"
                       rel="prev"
                       data-slot="pagination-link"
                       @mouseenter="setHover($el)"
                       class="relative z-10 h-9 pl-2 pr-3 gap-1.5 text-sm font-medium rounded-lg inline-flex items-center justify-center text-neutral-700 dark:text-neutral-300 hover:text-neutral-950 dark:hover:text-white transition-colors active:scale-95"
                       aria-label="{{ __('pagination.previous') }}">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline">Trước</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li data-slot="pagination-item">
                        <span aria-hidden="true"
                              data-slot="pagination-ellipsis"
                              class="size-9 flex items-center justify-center text-neutral-400 dark:text-neutral-500">
                            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <circle cx="8" cy="12" r="0.75" fill="currentColor"/>
                                <circle cx="12" cy="12" r="0.75" fill="currentColor"/>
                                <circle cx="16" cy="12" r="0.75" fill="currentColor"/>
                            </svg>
                            <span class="sr-only">More pages</span>
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li data-slot="pagination-item">
                                <span aria-current="page"
                                      data-slot="pagination-link"
                                      data-active="true"
                                      class="relative z-10 size-9 inline-flex items-center justify-center rounded-lg text-sm font-bold bg-neutral-900 text-white dark:bg-white dark:text-neutral-950 shadow-sm transition-all select-none">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li data-slot="pagination-item">
                                <a href="{{ $url }}"
                                   data-slot="pagination-link"
                                   @mouseenter="setHover($el)"
                                   class="relative z-10 size-9 inline-flex items-center justify-center rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-950 dark:hover:text-white transition-colors active:scale-95"
                                   aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li data-slot="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}"
                       rel="next"
                       data-slot="pagination-link"
                       @mouseenter="setHover($el)"
                       class="relative z-10 h-9 pl-3 pr-2 gap-1.5 text-sm font-medium rounded-lg inline-flex items-center justify-center text-neutral-700 dark:text-neutral-300 hover:text-neutral-950 dark:hover:text-white transition-colors active:scale-95"
                       aria-label="{{ __('pagination.next') }}">
                        <span class="hidden sm:inline">Sau</span>
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </li>
            @else
                <li data-slot="pagination-item">
                    <span aria-disabled="true"
                          data-slot="pagination-link"
                          class="h-9 pl-3 pr-2 gap-1.5 text-sm font-medium rounded-lg inline-flex items-center justify-center text-neutral-300 dark:text-neutral-600 cursor-not-allowed select-none opacity-50">
                        <span class="hidden sm:inline">Sau</span>
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
@endif
