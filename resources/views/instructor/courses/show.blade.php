@extends('layouts.instructor')

@section('title', 'Nội dung khóa học')
@section('page_title', 'Quản lý nội dung khóa học')

@section('content')
@php
    $canEdit = auth()->user()->can('update', $course);
    $quizzesByChapter = $course->quizzes->groupBy('chapter_id');
@endphp

<div class="space-y-6">
    <!-- Header khóa học -->
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-32 h-20 rounded-xl bg-gray-100 dark:bg-neutral-800 overflow-hidden shrink-0 hidden sm:block">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        @include('instructor.partials.status-badge', ['status' => $course->status])
                        <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $course->category->name ?? 'Chưa phân loại' }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">{{ $course->title }}</h2>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                        {{ $course->chapters->count() }} chương · {{ $course->lessons_count }} bài học · {{ $course->quizzes->count() }} quiz ·
                        <a href="{{ route('instructor.students.index', ['course_id' => $course->id]) }}" class="text-brand-700 dark:text-brand-400 hover:underline">{{ $course->enrollments_count }} học viên</a>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($course->isPublished())
                    <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="px-4 py-2 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Xem trang công khai</a>
                @endif
                @if($canEdit)
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="px-4 py-2 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Sửa thông tin</a>
                @endif
                @can('submit', $course)
                    <form method="POST" action="{{ route('instructor.courses.submit', $course) }}" onsubmit="return confirm('Gửi khóa học cho quản trị viên duyệt? Trong lúc chờ duyệt bạn sẽ không thể chỉnh sửa.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-700 text-white text-sm font-semibold shadow-sm hover:bg-brand-800">Gửi duyệt</button>
                    </form>
                @endcan
                @can('withdraw', $course)
                    <form method="POST" action="{{ route('instructor.courses.withdraw', $course) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold shadow-sm hover:bg-amber-600">Rút lại yêu cầu duyệt</button>
                    </form>
                @endcan
            </div>
        </div>

        @if($course->isPending())
            <div class="mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-sm text-amber-800 dark:text-amber-300">
                Khóa học đang chờ quản trị viên duyệt nên tạm thời không thể chỉnh sửa. Rút lại yêu cầu nếu bạn cần sửa nội dung.
            </div>
        @elseif($course->isRejected())
            <div class="mt-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-sm text-rose-800 dark:text-rose-300">
                <strong>Bị từ chối:</strong> {{ $course->reject_reason ?: 'Không có lý do cụ thể.' }} — Hãy chỉnh sửa và gửi duyệt lại.
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Đề cương: chương & bài học -->
        <div class="xl:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100">Đề cương khóa học</h3>
            </div>

            @forelse($course->chapters as $chapter)
                <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden" x-data="{ renaming: false }">
                    <!-- Header chương -->
                    <div class="px-5 py-3 bg-gray-50 dark:bg-neutral-800/40 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <span class="text-xs font-bold text-brand-700 dark:text-brand-400 uppercase shrink-0">Chương {{ $loop->iteration }}</span>
                            <h4 class="font-semibold text-gray-900 dark:text-neutral-100 truncate" x-show="!renaming">{{ $chapter->title }}</h4>
                            @if($canEdit)
                                <form method="POST" action="{{ route('instructor.chapters.update', $chapter) }}" x-show="renaming" x-cloak class="flex items-center gap-2 flex-1">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="title" value="{{ $chapter->title }}" required maxlength="255" class="flex-1 rounded-lg border-gray-300 dark:border-neutral-700 text-sm py-1.5 focus:border-brand-600 focus:ring-brand-600">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-brand-700 text-white text-xs font-semibold">Lưu</button>
                                    <button type="button" @click="renaming = false" class="px-3 py-1.5 rounded-lg bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-neutral-200 text-xs font-semibold">Hủy</button>
                                </form>
                            @endif
                        </div>

                        @if($canEdit)
                            <div class="flex items-center gap-1 shrink-0" x-show="!renaming">
                                        @unless($loop->first)
                                            <form method="POST" action="{{ route('instructor.chapters.move', $chapter) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" title="Lên trên" class="p-1.5 rounded-lg text-gray-500 dark:text-neutral-400 hover:bg-gray-200 dark:hover:bg-neutral-800 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                                </button>
                                            </form>
                                        @endunless
                                        @unless($loop->last)
                                            <form method="POST" action="{{ route('instructor.chapters.move', $chapter) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" title="Xuống dưới" class="p-1.5 rounded-lg text-gray-500 dark:text-neutral-400 hover:bg-gray-200 dark:hover:bg-neutral-800 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                            </form>
                                        @endunless
                                <button type="button" @click="renaming = true" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-800">Đổi tên</button>
                                <form method="POST" action="{{ route('instructor.chapters.destroy', $chapter) }}" onsubmit="return confirm('Xóa chương này cùng toàn bộ bài học bên trong?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15">Xóa</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Danh sách bài học -->
                    <ul class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($chapter->lessons as $lesson)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-gray-50 dark:hover:bg-neutral-800">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 text-xs font-bold flex items-center justify-center shrink-0">{{ $loop->iteration }}</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-neutral-100 truncate">{{ $lesson->title }}</p>
                                        <div class="text-xs text-gray-500 dark:text-neutral-400 flex items-center gap-3 mt-0.5">
                                            @if($lesson->video_url)
                                                <span class="inline-flex items-center gap-1 text-slate-500 dark:text-neutral-400">
                                                    <svg class="w-3.5 h-3.5 text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Video</span>
                                                </span>
                                            @endif
                                            @if($lesson->attachment_path)
                                                <span class="inline-flex items-center gap-1 text-slate-500 dark:text-neutral-400">
                                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    <span>Tài liệu</span>
                                                </span>
                                            @endif
                                            @if($lesson->content)
                                                <span class="inline-flex items-center gap-1 text-slate-500 dark:text-neutral-400">
                                                    <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Nội dung</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($canEdit)
                                    <div class="flex items-center gap-1 shrink-0">
                                        @unless($loop->first)
                                            <form method="POST" action="{{ route('instructor.lessons.move', $lesson) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" title="Lên trên" class="p-1.5 rounded-lg text-gray-500 dark:text-neutral-400 hover:bg-gray-200 dark:hover:bg-neutral-800 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                                </button>
                                            </form>
                                        @endunless
                                        @unless($loop->last)
                                            <form method="POST" action="{{ route('instructor.lessons.move', $lesson) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" title="Xuống dưới" class="p-1.5 rounded-lg text-gray-500 dark:text-neutral-400 hover:bg-gray-200 dark:hover:bg-neutral-800 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                </button>
                                            </form>
                                        @endunless
                                        <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] hover:bg-[#019D91]/10 transition">Sửa</a>
                                        <form method="POST" action="{{ route('instructor.lessons.destroy', $lesson) }}" onsubmit="return confirm('Xóa bài học này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 transition">Xóa</button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="px-5 py-4 text-sm text-gray-500 dark:text-neutral-400">Chương này chưa có bài học.</li>
                        @endforelse

                        <!-- Quiz cuối chương -->
                        @foreach($quizzesByChapter->get($chapter->id, collect()) as $quiz)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 bg-[#019D91]/5 dark:bg-[#019D91]/10 border-t border-[#019D91]/10">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-[#019D91]/15 text-[#018076] dark:text-[#02b3a5] text-xs font-bold flex items-center justify-center shrink-0">Q</span>
                                    <p class="text-sm font-medium text-gray-900 dark:text-neutral-100 truncate">{{ $quiz->title }} <span class="text-xs text-gray-500 dark:text-neutral-400">({{ $quiz->questions_count }} câu hỏi)</span></p>
                                </div>
                                <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] hover:bg-[#019D91]/15 shrink-0 transition">Quản lý câu hỏi</a>
                            </li>
                        @endforeach
                    </ul>

                    @if($canEdit)
                        <div class="px-5 py-3 border-t border-gray-100 dark:border-neutral-800 flex flex-wrap gap-2">
                            <a href="{{ route('instructor.chapters.lessons.create', $chapter) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-800 dark:text-neutral-200 text-xs font-semibold hover:bg-gray-200 dark:hover:bg-neutral-700 transition">+ Thêm bài học</a>
                            <a href="{{ route('instructor.courses.quizzes.create', [$course, 'chapter_id' => $chapter->id]) }}" class="px-3 py-1.5 rounded-lg bg-[#019D91]/10 text-[#019D91] dark:text-[#02b3a5] text-xs font-semibold hover:bg-[#019D91]/20 transition">+ Thêm quiz cho chương</a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-neutral-900 p-10 rounded-2xl shadow-sm border border-dashed border-gray-300 dark:border-neutral-700 text-center text-gray-500 dark:text-neutral-400">
                    Khóa học chưa có chương nào. Hãy thêm chương đầu tiên.
                </div>
            @endforelse

            @if($canEdit)
                <form method="POST" action="{{ route('instructor.courses.chapters.store', $course) }}" class="bg-white dark:bg-neutral-900 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text" name="title" required maxlength="255" placeholder="Tên chương mới, VD: Chương 1: Giới thiệu"
                           class="flex-1 rounded-xl border-gray-300 dark:border-neutral-700 text-sm focus:border-brand-600 focus:ring-brand-600">
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-700 text-white text-sm font-semibold hover:bg-brand-800">+ Thêm chương</button>
                </form>
            @endif
        </div>

        <!-- Cột phải: danh sách quiz -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100">Quiz trắc nghiệm</h3>
                @if($canEdit)
                    <a href="{{ route('instructor.courses.quizzes.create', $course) }}" class="px-3 py-1.5 rounded-lg bg-[#019D91] text-white text-xs font-semibold hover:bg-[#008b80] transition shadow-sm">+ Tạo quiz</a>
                @endif
            </div>

            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 divide-y divide-gray-100 dark:divide-neutral-800">
                @forelse($course->quizzes as $quiz)
                    <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="block px-5 py-4 hover:bg-gray-50 dark:hover:bg-neutral-800">
                        <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100">{{ $quiz->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                            {{ $quiz->chapter->title ?? 'Quiz chung của khóa' }} · {{ $quiz->questions_count }} câu hỏi
                        </p>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-gray-500 dark:text-neutral-400 text-center">Chưa có quiz nào.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
