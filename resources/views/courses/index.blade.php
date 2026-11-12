@extends('layouts.public')

@section('title', 'Danh sách Khóa học')

@section('content')
<div class="w-full min-h-screen bg-white dark:bg-[#0d1117]">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ===== SIDEBAR BỘ LỌC (GitHub Dark Bordered Surface) ===== --}}
        <aside class="w-full lg:w-80 shrink-0" x-data="{ filtersOpen: false, isCollapsed: false }" x-init="filtersOpen = window.matchMedia('(min-width: 1024px)').matches">
            <button type="button"
                    @click="filtersOpen = !filtersOpen"
                    class="lg:hidden w-full flex items-center justify-between px-4 py-3 mb-3 rounded-2xl bg-white dark:bg-[#161b22] border border-gray-100 dark:border-[#30363d] shadow-sm text-sm font-semibold text-gray-900 dark:text-[#f0f6fc]">
                <span>Bộ lọc tìm kiếm</span>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': filtersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />
                </svg>
            </button>
            <form method="GET" action="{{ url('/courses') }}" id="filter-form">
                <div x-show="filtersOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-[#30363d] rounded-2xl shadow-sm flex flex-col sticky top-24 transition-colors z-20">
                    <!-- Header bộ lọc kèm nút thu gọn -->
                    <div class="p-6 border-b border-gray-50 dark:border-[#30363d] rounded-t-2xl flex items-center justify-between cursor-pointer select-none"
                         @click="isCollapsed = !isCollapsed">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-[#f0f6fc] tracking-tight">Bộ lọc tìm kiếm</h3>
                            <p class="text-xs text-gray-400 mt-0.5" x-show="!isCollapsed">Tùy chỉnh kết quả tìm kiếm của bạn</p>
                        </div>
                        <!-- Nút thu gọn / mở rộng -->
                        <button type="button"
                                @click.stop="isCollapsed = !isCollapsed"
                                class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-neutral-800 transition"
                                :title="isCollapsed ? 'Mở rộng bộ lọc' : 'Thu gọn bộ lọc'">
                            <svg class="w-5 h-5 transition-transform duration-200"
                                 :class="isCollapsed ? '-rotate-90' : 'rotate-0'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Nội dung các trường lọc -->
                    <div x-show="!isCollapsed"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="p-6 space-y-8">
                    <div class="space-y-3">
                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Từ khóa</label>
                        <div class="relative group">
                            <svg class="absolute inset-y-0 left-3 my-auto w-4 h-4 text-gray-400 group-focus-within:text-gray-900 dark:group-focus-within:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m21 21-4.35-4.35m2.1-5.15a7.25 7.25 0 1 1-14.5 0 7.25 7.25 0 0 1 14.5 0Z" />
                            </svg>
                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                   placeholder="Tìm tên khóa học..."
                                   class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-[#0d1117] border border-transparent rounded-xl text-gray-900 dark:text-[#f0f6fc] focus:outline-none focus:bg-white dark:focus:bg-[#161b22] focus:border-gray-200 dark:focus:border-[#30363d] transition-all placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Danh mục (Combobox Animate-UI) --}}
                    @php
                        $selectedCatId = request('category', '');
                        $selectedCat = $categories->firstWhere('id', $selectedCatId);
                        $initialCatLabel = $selectedCat ? $selectedCat->name . ' (' . $selectedCat->courses_count . ')' : 'Tất cả danh mục';
                    @endphp
                    <div class="space-y-3 relative"
                         x-data="{
                             open: false,
                             selectedValue: '{{ $selectedCatId }}',
                             selectedLabel: '{{ addslashes($initialCatLabel) }}',
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                                 this.$nextTick(() => {
                                     this.$el.closest('form').submit();
                                 });
                             }
                         }">
                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Danh mục</label>

                        <!-- Hidden Input for Form Submission -->
                        <input type="hidden" name="category" :value="selectedValue">

                        <!-- Combobox Trigger Button -->
                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                data-slot="combobox-trigger"
                                class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#0d1117] text-gray-900 dark:text-[#f0f6fc] text-sm flex items-center justify-between text-left focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 focus:border-gray-400 dark:focus:border-gray-500 transition-all cursor-pointer select-none group/combobox-trigger shadow-xs">
                            <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-gray-200"></span>
                            <!-- ArrowDown01Icon (Neutral) -->
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-gray-300 shrink-0 pointer-events-none"
                                 :class="open ? 'rotate-180 text-gray-700 dark:text-gray-300' : ''"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Combobox Content Popup (Animate-UI Floating Dropdown đè lên khung) -->
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             @keydown.escape.window="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             data-slot="combobox-content"
                             class="absolute left-0 right-0 top-full mt-1.5 z-40 max-h-64 overflow-y-auto rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 space-y-0.5">
                            
                            <!-- Option: Tất cả danh mục -->
                            <button type="button"
                                    @click="select('', 'Tất cả danh mục')"
                                    data-slot="combobox-item"
                                    class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                    :class="selectedValue === '' 
                                        ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                        : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                <span>Tất cả danh mục</span>
                                <svg x-show="selectedValue === ''"
                                     class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2.2"
                                     viewBox="0 0 24 24"
                                     aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            @foreach($categories as $category)
                                @php
                                    $catLabel = $category->name . ' (' . $category->courses_count . ')';
                                @endphp
                                <button type="button"
                                        @click="select('{{ $category->id }}', '{{ addslashes($catLabel) }}')"
                                        data-slot="combobox-item"
                                        class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === '{{ $category->id }}' 
                                            ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                    <span class="truncate">{{ $catLabel }}</span>
                                    <svg x-show="selectedValue === '{{ $category->id }}'"
                                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2.2"
                                         viewBox="0 0 24 24"
                                         aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Giá --}}
                    <div class="space-y-3">
                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Mức phí</label>
                        <div data-slot="radio-group" class="space-y-2.5">
                            @foreach([''=>'Tất cả', 'free'=>'Miễn phí', 'paid'=>'Có phí'] as $value => $label)
                            <label class="flex items-center gap-3 group cursor-pointer select-none">
                                <input type="radio" name="price" value="{{ $value }}"
                                       {{ request('price', '') === $value ? 'checked' : '' }}
                                       class="peer sr-only">
                                <span data-slot="radio-group-item" class="relative size-4 rounded-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50 flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400 peer-checked:border-neutral-900 dark:peer-checked:border-white peer-checked:bg-neutral-900 dark:peer-checked:bg-white peer-checked:[&>span>span]:opacity-100 peer-checked:[&>span>span]:scale-100 peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-900/20">
                                    <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                        <span class="size-2 rounded-full bg-white dark:bg-neutral-950 opacity-0 scale-0 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"></span>
                                    </span>
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors peer-checked:text-neutral-950 dark:peer-checked:text-white peer-checked:font-semibold">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Trình độ --}}
                    <div class="space-y-3">
                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Trình độ</label>
                        <div data-slot="radio-group" class="grid grid-cols-2 gap-2">
                            @foreach(['' => 'Tất cả', 'beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'] as $value => $label)
                            <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-100 dark:border-[#30363d] hover:border-gray-200 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-[#0d1117] transition-all cursor-pointer group select-none">
                                <input type="radio" name="level" value="{{ $value }}"
                                       {{ request('level', '') === $value ? 'checked' : '' }}
                                       class="peer sr-only">
                                <span data-slot="radio-group-item" class="relative size-4 rounded-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50 flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400 peer-checked:border-neutral-900 dark:peer-checked:border-white peer-checked:bg-neutral-900 dark:peer-checked:bg-white peer-checked:[&>span>span]:opacity-100 peer-checked:[&>span>span]:scale-100 peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-900/20">
                                    <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                        <span class="size-2 rounded-full bg-white dark:bg-neutral-950 opacity-0 scale-0 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"></span>
                                    </span>
                                </span>
                                <span class="text-[13px] text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors peer-checked:text-neutral-950 dark:peer-checked:text-white peer-checked:font-semibold">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sắp xếp (Combobox Animate-UI) --}}
                    @php
                        $sortOptions = [
                            'newest' => 'Mới nhất',
                            'rating' => 'Đánh giá cao nhất',
                            'popular' => 'Phổ biến nhất',
                        ];
                        $initialSort = request('sort', 'newest');
                        $initialLabel = $sortOptions[$initialSort] ?? 'Mới nhất';
                    @endphp
                    <div class="space-y-3 relative"
                         x-data="{
                             open: false,
                             selectedValue: '{{ $initialSort }}',
                             selectedLabel: '{{ $initialLabel }}',
                             options: [
                                 { value: 'newest', label: 'Mới nhất' },
                                 { value: 'rating', label: 'Đánh giá cao nhất' },
                                 { value: 'popular', label: 'Phổ biến nhất' }
                             ],
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                                 this.$nextTick(() => {
                                     this.$el.closest('form').submit();
                                 });
                             }
                         }">
                        <label class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Sắp xếp</label>

                        <!-- Hidden Input for Form Submission -->
                        <input type="hidden" name="sort" :value="selectedValue">

                        <!-- Combobox Trigger Button -->
                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                data-slot="combobox-trigger"
                                class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#0d1117] text-gray-900 dark:text-[#f0f6fc] text-sm flex items-center justify-between text-left focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 focus:border-gray-400 dark:focus:border-gray-500 transition-all cursor-pointer select-none group/combobox-trigger shadow-xs">
                            <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-gray-200"></span>
                            <!-- ArrowDown01Icon (Neutral) -->
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-gray-300 shrink-0 pointer-events-none"
                                 :class="open ? 'rotate-180 text-gray-700 dark:text-gray-300' : ''"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24"
                                 aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Combobox Content Popup (Animate-UI Floating Dropdown đè lên khung) -->
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             @keydown.escape.window="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             data-slot="combobox-content"
                             class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 overflow-hidden space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button"
                                        @click="select(item.value, item.label)"
                                        data-slot="combobox-item"
                                        class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === item.value 
                                            ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                    <span x-text="item.label"></span>
                                    <!-- Tick02Icon Checkmark Indicator (Neutral, không đổi qua xanh) -->
                                    <svg x-show="selectedValue === item.value"
                                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                         fill="none"
                                         stroke="currentColor"
                                         stroke-width="2.2"
                                         viewBox="0 0 24 24"
                                         aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                    </div>

                    <div x-show="!isCollapsed"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="p-6 bg-white dark:bg-[#161b22] border-t border-gray-50 dark:border-[#30363d] space-y-3 rounded-b-2xl">
                        <button type="submit" class="w-full py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-xl hover:bg-black dark:hover:bg-gray-100 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-xs">
                            <span>Áp dụng bộ lọc</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" /></svg>
                        </button>
                        <a href="{{ url('/courses') }}" class="w-full py-2.5 bg-transparent text-gray-500 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-[#0d1117] hover:text-gray-900 dark:hover:text-white transition-all text-center block">
                            Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 min-w-0">
            {{-- Header info (Đã xóa nút Làm mới theo yêu cầu) --}}
            <div class="flex items-end justify-between gap-4 mb-6 pb-4 border-b border-[#E5E5EA] dark:border-[#30363d]">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[#1D1D1F] dark:text-[#f0f6fc]">
                        Tất cả khóa học
                    </h1>
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e] mt-1">
                        Hiện có <span class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc]">{{ $courses->total() }}</span> khóa học phù hợp
                        @if(request('keyword'))
                            với từ khóa "<span class="text-[#019D91] dark:text-[#02b3a5]">{{ request('keyword') }}</span>"
                        @endif
                    </p>
                </div>
            </div>

            {{-- Course Grid --}}
            @if($courses->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($courses as $course)
                        @include('partials.course-card', ['course' => $course])
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12 flex justify-center">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-24 text-center bg-[#F5F5F7] dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl p-8">
                    <div class="w-16 h-16 bg-white dark:bg-[#0d1117] border border-[#E5E5EA] dark:border-[#30363d] rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-[#86868B] dark:text-[#7d8590]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-[#1D1D1F] dark:text-[#f0f6fc] mb-1">Không tìm thấy khóa học nào</h3>
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e] max-w-sm mb-6">Hãy thử tìm với từ khóa khác hoặc điều chỉnh lại các tiêu chí bộ lọc</p>
                    <a href="{{ url('/courses') }}" class="btn-apple-primary px-5 py-2 text-xs">
                        Xóa tất cả bộ lọc
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
