@php
    $levelOptions = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Cột trái: thông tin chính -->
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 space-y-5">
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Tên khóa học <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required maxlength="255"
                       placeholder="VD: Lập trình Laravel từ cơ bản đến nâng cao"
                       class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600">
                @error('title') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Mô tả khóa học</label>
                <textarea id="description" name="description" data-editor data-upload-url="{{ route('upload.image') }}"
                          placeholder="Giới thiệu nội dung, mục tiêu và đối tượng của khóa học...">{{ old('description', $course->description) }}</textarea>
                @error('description') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Cột phải: thiết lập + ảnh -->
    <div class="space-y-6">
        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 space-y-5">
            @php
                $currentCatId = (string) old('category_id', $course->category_id ?? '');
                $currentCat = $categories->firstWhere('id', $currentCatId);
                $catLabel = $currentCat ? $currentCat->name : '-- Chưa phân loại --';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $currentCatId }}',
                     selectedLabel: '{{ addslashes($catLabel) }}',
                     options: [
                         { value: '', label: '-- Chưa phân loại --' },
                         @foreach($categories as $category)
                             { value: '{{ $category->id }}', label: '{{ addslashes($category->name) }}' },
                         @endforeach
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Danh mục</label>
                <input type="hidden" name="category_id" :value="selectedValue">

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
                @error('category_id') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            @php
                $currentLevelVal = old('level', $course->level ?? 'beginner');
                $currentLevelLabel = $levelOptions[$currentLevelVal] ?? 'Cơ bản';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $currentLevelVal }}',
                     selectedLabel: '{{ addslashes($currentLevelLabel) }}',
                     options: [
                         @foreach($levelOptions as $value => $label)
                             { value: '{{ $value }}', label: '{{ addslashes($label) }}' },
                         @endforeach
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <label class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Trình độ <span class="text-rose-500">*</span></label>
                <input type="hidden" name="level" :value="selectedValue">

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
                @error('level') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Học phí (VNĐ) <span class="text-rose-500">*</span></label>
                <input type="number" id="price" name="price" min="0" step="1000" required
                       value="{{ old('price', $course->price !== null ? (int) $course->price : 0) }}"
                       class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600">
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Nhập 0 nếu khóa học miễn phí.</p>
                @error('price') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 space-y-5"
             x-data="{ thumbPreview: null, galleryPreviews: [] }">
            <div>
                <label for="thumbnail" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-2">Ảnh đại diện (thumbnail)</label>
                <div class="aspect-video w-full rounded-xl bg-gray-100 dark:bg-neutral-800 overflow-hidden mb-3 flex items-center justify-center">
                    <template x-if="thumbPreview">
                        <img :src="thumbPreview" class="w-full h-full object-cover" alt="Xem trước">
                    </template>
                    <template x-if="!thumbPreview">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/'.$course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->title }}">
                        @else
                            <span class="text-sm text-gray-400 dark:text-neutral-500">Chưa có ảnh</span>
                        @endif
                    </template>
                </div>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                       @change="thumbPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                       class="block w-full text-sm text-gray-600 dark:text-neutral-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-800 file:font-semibold hover:file:bg-brand-100">
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">JPG, PNG, WEBP. Tối đa 5MB.</p>
                @error('thumbnail') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="images" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-2">Thư viện ảnh (chọn nhiều ảnh)</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple
                       @change="galleryPreviews = Array.from($event.target.files).map(file => URL.createObjectURL(file))"
                       class="block w-full text-sm text-gray-600 dark:text-neutral-300 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-800 file:font-semibold hover:file:bg-brand-100">
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Tối đa 10 ảnh mỗi lần tải lên, mỗi ảnh 5MB.</p>
                @error('images') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                @error('images.*') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror

                <div class="grid grid-cols-3 gap-2 mt-3" x-show="galleryPreviews.length" x-cloak>
                    <template x-for="src in galleryPreviews" :key="src">
                        <img :src="src" class="aspect-video w-full object-cover rounded-lg ring-2 ring-brand-200 dark:ring-brand-500/30" alt="Ảnh mới">
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/editor.js')
@endpush
