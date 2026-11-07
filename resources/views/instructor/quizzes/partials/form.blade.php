<div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 space-y-5 max-w-3xl">
    <div>
        <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Tên quiz <span class="text-rose-500">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $quiz->title) }}" required maxlength="255"
               placeholder="VD: Kiểm tra cuối chương 1"
               class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600">
        @error('title') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="chapter_id" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Thuộc chương</label>
        <select id="chapter_id" name="chapter_id" class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600">
            <option value="">-- Quiz chung của khóa học --</option>
            @foreach($course->chapters as $chapterOption)
                <option value="{{ $chapterOption->id }}" @selected((string) old('chapter_id', $quiz->chapter_id) === (string) $chapterOption->id)>{{ $chapterOption->title }}</option>
            @endforeach
        </select>
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
