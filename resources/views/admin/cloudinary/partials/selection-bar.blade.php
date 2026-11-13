{{--
    Thanh chọn nhiều ảnh, đặt ngay trên lưới ảnh, dùng chung cho Thư viện ảnh và Thùng rác.
    Chưa tích ô nào: chỉ là dòng hướng dẫn. Tích ít nhất 1 ô: hiện "Chọn tất cả", số ảnh đã chọn
    và các nút thao tác ($slot), đồng thời dính trên đầu vùng nội dung khi cuộn xuống.
    Phải nằm trong x-data="cloudinarySelection(...)".

    Biến: $hint (dòng hướng dẫn), $total (số ảnh ở mọi trang), $hasPages (có nhiều trang hay không).
--}}
<div class="min-h-[56px] rounded-2xl px-3 sm:px-4 py-2 flex flex-wrap items-center gap-x-4 gap-y-2"
     :class="selected.length
        ? 'sticky -top-4 sm:-top-6 lg:-top-8 z-30 bg-white/95 dark:bg-neutral-900/95 backdrop-blur border border-[#019D91]/50 dark:border-[#02b3a5]/40 shadow-lg'
        : 'border border-dashed border-gray-200 dark:border-neutral-800'">
    <p x-show="!selected.length" class="flex items-center gap-2 text-xs text-gray-500 dark:text-neutral-400">
        <iconify-icon icon="lucide:square-check" class="text-base text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
        {{ $hint }}
    </p>

    <label x-show="selected.length" x-cloak class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-neutral-100 cursor-pointer select-none">
        <input type="checkbox" :checked="allOnPage()" @change="toggleAllOnPage()"
               class="w-4 h-4 rounded border-gray-300 dark:border-neutral-700 text-[#019D91] dark:text-[#02b3a5] focus:ring-[#019D91]">
        Chọn tất cả
        @if($hasPages)
            <span class="text-xs font-normal text-gray-500 dark:text-neutral-400">(trang này)</span>
        @endif
    </label>

    <span x-show="selected.length" x-cloak class="text-sm text-gray-600 dark:text-neutral-300">
        Đã chọn <strong class="text-gray-900 dark:text-neutral-100" x-text="selected.length"></strong> ảnh
    </span>

    @if($hasPages)
        <button type="button" x-show="selected.length && allOnPage() && !everything()" x-cloak @click="selectEverything()"
                class="text-sm font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline">
            Chọn luôn cả {{ $total }} ảnh ở mọi trang
        </button>
        <span x-show="selected.length && everything()" x-cloak class="text-xs font-medium text-[#019D91] dark:text-[#02b3a5]">
            Đã chọn hết {{ $total }} ảnh ở mọi trang
        </span>
    @endif

    <div x-show="selected.length" x-cloak class="flex flex-wrap items-center gap-2 ml-auto">
        <button type="button" @click="clear()"
                class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-neutral-300 rounded-xl hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors whitespace-nowrap">
            Bỏ chọn
        </button>
        {{ $slot }}
    </div>
</div>

@once
    @push('scripts')
    <script>
        // Chọn nhiều ảnh. Khóa (key) là đường dẫn ảnh (thư viện) hoặc id (thùng rác), luôn ở dạng chuỗi.
        function cloudinarySelection(config) {
            return {
                selected: [],
                pageKeys: config.pageKeys || [],
                allKeys: config.allKeys || [],
                usedKeys: config.usedKeys || [],
                isSelected(key) {
                    return this.selected.includes(key);
                },
                toggle(key) {
                    this.selected = this.isSelected(key)
                        ? this.selected.filter((item) => item !== key)
                        : [...this.selected, key];
                },
                allOnPage() {
                    return this.pageKeys.length > 0 && this.pageKeys.every((key) => this.selected.includes(key));
                },
                everything() {
                    return this.allKeys.length > 0 && this.allKeys.every((key) => this.selected.includes(key));
                },
                toggleAllOnPage() {
                    this.selected = this.allOnPage()
                        ? this.selected.filter((key) => !this.pageKeys.includes(key))
                        : [...new Set([...this.selected, ...this.pageKeys])];
                },
                selectEverything() {
                    this.selected = [...this.allKeys];
                },
                clear() {
                    this.selected = [];
                },
                usedCount() {
                    return this.selected.filter((key) => this.usedKeys.includes(key)).length;
                },
            };
        }
    </script>
    @endpush
@endonce
