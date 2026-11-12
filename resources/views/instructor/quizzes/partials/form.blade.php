<div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 space-y-5 max-w-3xl">
    <div>
        <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Tên quiz <span class="text-rose-500">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255"
               placeholder="VD: Kiểm tra cuối chương 1"
               class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600">
        @error('title') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>

    @php
        $currentQuizChapId = (string) old('chapter_id', $quiz->chapter_id ?? '');
        $currentQuizChap = $course->chapters->firstWhere('id', $currentQuizChapId);
        $quizChapLabel = $currentQuizChap ? $currentQuizChap->title : '-- Quiz chung của khóa học --';
    @endphp
    <div class="relative"
         x-data="{
             open: false,
             selectedValue: '{{ $currentQuizChapId }}',
             selectedLabel: '{{ addslashes($quizChapLabel) }}',
             options: [
                 { value: '', label: '-- Quiz chung của khóa học --' },
                 @foreach($course->chapters as $chapterOption)
                     { value: '{{ $chapterOption->id }}', label: '{{ addslashes($chapterOption->title) }}' },
                 @endforeach
             ],
             select(val, label) {
                 this.selectedValue = val;
                 this.selectedLabel = label;
                 this.open = false;
             }
         }">
        <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Thuộc chương</label>
        <input type="hidden" name="chapter_id" :value="selectedValue">

        <!-- Combobox Trigger Button -->
        <button type="button"
                @click="open = !open"
                :aria-expanded="open"
                data-slot="combobox-trigger"
                class="w-full h-11 px-3.5 rounded-xl border border-gray-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-900 dark:text-white text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-600 transition cursor-pointer select-none group/combobox-trigger shadow-2xs">
            <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-neutral-200"></span>
            <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-neutral-300 shrink-0 pointer-events-none"
                 :class="open ? 'rotate-180 text-gray-700 dark:text-neutral-300' : ''"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
            </svg>
        </button>

        <!-- Combobox Content Popup -->
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
             class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
            <template x-for="item in options" :key="item.value">
                <button type="button"
                        @click="select(item.value, item.label)"
                        data-slot="combobox-item"
                        class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                        :class="selectedValue === item.value 
                            ? 'bg-neutral-100 dark:bg-neutral-700/70 text-neutral-900 dark:text-white font-medium' 
                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700/40'">
                    <span x-text="item.label"></span>
                    <svg x-show="selectedValue === item.value"
                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none"
                         fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </template>
        </div>
        @error('chapter_id') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Mô tả / hướng dẫn</label>
        <textarea id="description" name="description" rows="4" maxlength="2000"
                  placeholder="Hướng dẫn làm bài cho học viên..."
                  class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600">{{ old('description', $quiz->description) }}</textarea>
        @error('description') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
