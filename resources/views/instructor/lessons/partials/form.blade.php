<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Tên bài học <span class="text-rose-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $lesson->title) }}" required maxlength="255"
                   placeholder="VD: Bài 1: Cài đặt môi trường"
                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-semibold text-gray-700 mb-1">Nội dung bài học</label>
            <p class="text-xs text-gray-500 mb-2">Soạn thảo bằng CKEditor 5: chèn ảnh, bảng, đoạn code và video.</p>
            <textarea id="content" name="content" data-editor data-upload-url="{{ route('upload.image') }}"
                      placeholder="Nội dung chi tiết của bài học...">{{ old('content', $lesson->content) }}</textarea>
            @error('content') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
            @isset($chapters)
                <div>
                    <label for="chapter_id" class="block text-sm font-semibold text-gray-700 mb-1">Thuộc chương</label>
                    <select id="chapter_id" name="chapter_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($chapters as $chapterOption)
                            <option value="{{ $chapterOption->id }}" @selected((string) old('chapter_id', $lesson->chapter_id) === (string) $chapterOption->id)>{{ $chapterOption->title }}</option>
                        @endforeach
                    </select>
                    @error('chapter_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @else
                <div>
                    <p class="block text-sm font-semibold text-gray-700 mb-1">Thuộc chương</p>
                    <p class="text-sm text-gray-900">{{ $chapter->title }}</p>
                </div>
            @endisset

            <div x-data="{ url: @js(old('video_url', $lesson->video_url ?? '')) }">
                <label for="video_url" class="block text-sm font-semibold text-gray-700 mb-1">Link video YouTube</label>
                <input type="url" id="video_url" name="video_url" x-model="url"
                       placeholder="https://www.youtube.com/watch?v=..."
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('video_url') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror

                <template x-if="url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{11})/)">
                    <div class="mt-3 aspect-video rounded-xl overflow-hidden bg-black">
                        <iframe class="w-full h-full" allowfullscreen
                                :src="'https://www.youtube.com/embed/' + url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{11})/)[1]"></iframe>
                    </div>
                </template>
            </div>

            <div>
                <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-1">Tài liệu đính kèm</label>
                @if($lesson->attachment_path)
                    <div class="mb-2 p-3 rounded-xl bg-gray-50 border border-gray-200 text-sm">
                        <a href="{{ asset('storage/'.$lesson->attachment_path) }}" target="_blank" class="text-indigo-600 font-medium hover:underline break-all">📎 {{ basename($lesson->attachment_path) }}</a>
                        <label class="flex items-center gap-2 mt-2 text-xs text-gray-600">
                            <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
                            Xóa tài liệu hiện tại
                        </label>
                    </div>
                @endif
                <input type="file" id="attachment" name="attachment"
                       class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-semibold hover:file:bg-indigo-100">
                <p class="text-xs text-gray-500 mt-1">PDF, Word, PowerPoint, Excel, ZIP/RAR, TXT. Tối đa 20MB.</p>
                @error('attachment') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/editor.js')
@endpush
