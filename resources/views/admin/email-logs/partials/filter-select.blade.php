{{-- Ô chọn lọc (cùng kiểu với các trang admin khác). Biến: $name, $value, $options [value => label]. --}}
<div class="relative"
     x-data="{
         open: false,
         selectedValue: @js($value),
         selectedLabel: @js($options[$value] ?? reset($options)),
         options: @js(collect($options)->map(fn ($label, $key) => ['value' => (string) $key, 'label' => $label])->values()),
         select(val, label) {
             this.selectedValue = val;
             this.selectedLabel = label;
             this.open = false;
         }
     }">
    <input type="hidden" name="{{ $name }}" :value="selectedValue">
    <button type="button" @click="open = !open" data-slot="combobox-trigger"
            class="w-full h-10 px-3.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm text-gray-700 dark:text-neutral-200 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
        <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-neutral-200">{{ $options[$value] ?? reset($options) }}</span>
        <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-neutral-300 shrink-0 pointer-events-none"
             :class="open ? 'rotate-180 text-gray-700 dark:text-neutral-200' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
        </svg>
    </button>
    <div x-show="open" x-cloak @click.outside="open = false" @keydown.escape.window="open = false"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1 scale-98" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         data-slot="combobox-content" class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
        <template x-for="item in options" :key="item.value">
            <button type="button" @click="select(item.value, item.label)" data-slot="combobox-item"
                    class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                    :class="selectedValue === item.value ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700/40'">
                <span x-text="item.label"></span>
                <svg x-show="selectedValue === item.value" class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            </button>
        </template>
    </div>
</div>
