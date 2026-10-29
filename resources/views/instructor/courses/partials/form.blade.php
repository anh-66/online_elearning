@php
    $levelOptions = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Cột trái: thông tin chính -->
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Tên khóa học <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" required maxlength="255"
                       placeholder="VD: Lập trình Laravel từ cơ bản đến nâng cao"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Mô tả khóa học</label>
                <p class="text-xs text-gray-500 mb-2">Hỗ trợ chèn ảnh, bảng và video YouTube (nút “Insert media”).</p>
                <textarea id="description" name="description" data-editor data-upload-url="{{ route('upload.image') }}"
                          placeholder="Giới thiệu nội dung, mục tiêu và đối tượng của khóa học...">{{ old('description', $course->description) }}</textarea>
                @error('description') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Cột phải: thiết lập + ảnh -->
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
            <div>
                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Danh mục</label>
                <select id="category_id" name="category_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Chưa phân loại --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $course->category_id) === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="level" class="block text-sm font-semibold text-gray-700 mb-1">Trình độ <span class="text-rose-500">*</span></label>
                <select id="level" name="level" required class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($levelOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('level', $course->level) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('level') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Học phí (VNĐ) <span class="text-rose-500">*</span></label>
                <input type="number" id="price" name="price" min="0" step="1000" required
                       value="{{ old('price', $course->price !== null ? (int) $course->price : 0) }}"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Nhập 0 nếu khóa học miễn phí.</p>
                @error('price') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5"
             x-data="{ thumbPreview: null, galleryPreviews: [] }">
            <div>
                <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-2">Ảnh đại diện (thumbnail)</label>
                <div class="aspect-video w-full rounded-xl bg-gray-100 overflow-hidden mb-3 flex items-center justify-center">
                    <template x-if="thumbPreview">
                        <img :src="thumbPreview" class="w-full h-full object-cover" alt="Xem trước">
                    </template>
                    <template x-if="!thumbPreview">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/'.$course->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $course->title }}">
                        @else
                            <span class="text-sm text-gray-400">Chưa có ảnh</span>
                        @endif
                    </template>
                </div>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                       @change="thumbPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                       class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP. Tối đa 5MB.</p>
                @error('thumbnail') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="images" class="block text-sm font-semibold text-gray-700 mb-2">Thư viện ảnh (chọn nhiều ảnh)</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple
                       @change="galleryPreviews = Array.from($event.target.files).map(file => URL.createObjectURL(file))"
                       class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                <p class="text-xs text-gray-500 mt-1">Tối đa 10 ảnh mỗi lần tải lên, mỗi ảnh 5MB.</p>
                @error('images') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                @error('images.*') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror

                <div class="grid grid-cols-3 gap-2 mt-3" x-show="galleryPreviews.length" x-cloak>
                    <template x-for="src in galleryPreviews" :key="src">
                        <img :src="src" class="aspect-video w-full object-cover rounded-lg ring-2 ring-indigo-200" alt="Ảnh mới">
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/editor.js')
@endpush
