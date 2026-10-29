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
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-32 h-20 rounded-xl bg-gray-100 overflow-hidden shrink-0 hidden sm:block">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        @include('instructor.partials.status-badge', ['status' => $course->status])
                        <span class="text-xs text-gray-500">{{ $course->category->name ?? 'Chưa phân loại' }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $course->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $course->chapters->count() }} chương · {{ $course->lessons_count }} bài học · {{ $course->quizzes->count() }} quiz ·
                        <a href="{{ route('instructor.students.index', ['course_id' => $course->id]) }}" class="text-indigo-600 hover:underline">{{ $course->enrollments_count }} học viên</a>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($course->isPublished())
                    <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="px-4 py-2 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Xem trang công khai</a>
                @endif
                @if($canEdit)
                    <a href="{{ route('instructor.courses.edit', $course) }}" class="px-4 py-2 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Sửa thông tin</a>
                @endif
                @can('submit', $course)
                    <form method="POST" action="{{ route('instructor.courses.submit', $course) }}" onsubmit="return confirm('Gửi khóa học cho quản trị viên duyệt? Trong lúc chờ duyệt bạn sẽ không thể chỉnh sửa.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700">Gửi duyệt</button>
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
            <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800">
                Khóa học đang chờ quản trị viên duyệt nên tạm thời không thể chỉnh sửa. Rút lại yêu cầu nếu bạn cần sửa nội dung.
            </div>
        @elseif($course->isRejected())
            <div class="mt-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800">
                <strong>Bị từ chối:</strong> {{ $course->reject_reason ?: 'Không có lý do cụ thể.' }} — Hãy chỉnh sửa và gửi duyệt lại.
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Đề cương: chương & bài học -->
        <div class="xl:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Đề cương khóa học</h3>
            </div>

            @forelse($course->chapters as $chapter)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ renaming: false }">
                    <!-- Header chương -->
                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <span class="text-xs font-bold text-indigo-600 uppercase shrink-0">Chương {{ $loop->iteration }}</span>
                            <h4 class="font-semibold text-gray-900 truncate" x-show="!renaming">{{ $chapter->title }}</h4>
                            @if($canEdit)
                                <form method="POST" action="{{ route('instructor.chapters.update', $chapter) }}" x-show="renaming" x-cloak class="flex items-center gap-2 flex-1">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="title" value="{{ $chapter->title }}" required maxlength="255" class="flex-1 rounded-lg border-gray-300 text-sm py-1.5 focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold">Lưu</button>
                                    <button type="button" @click="renaming = false" class="px-3 py-1.5 rounded-lg bg-gray-200 text-gray-700 text-xs font-semibold">Hủy</button>
                                </form>
                            @endif
                        </div>

                        @if($canEdit)
                            <div class="flex items-center gap-1 shrink-0" x-show="!renaming">
                                @unless($loop->first)
                                    <form method="POST" action="{{ route('instructor.chapters.move', $chapter) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" title="Lên trên" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200">↑</button>
                                    </form>
                                @endunless
                                @unless($loop->last)
                                    <form method="POST" action="{{ route('instructor.chapters.move', $chapter) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" title="Xuống dưới" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200">↓</button>
                                    </form>
                                @endunless
                                <button type="button" @click="renaming = true" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-200">Đổi tên</button>
                                <form method="POST" action="{{ route('instructor.chapters.destroy', $chapter) }}" onsubmit="return confirm('Xóa chương này cùng toàn bộ bài học bên trong?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50">Xóa</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Danh sách bài học -->
                    <ul class="divide-y divide-gray-100">
                        @forelse($chapter->lessons as $lesson)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-gray-50">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 text-xs font-bold flex items-center justify-center shrink-0">{{ $loop->iteration }}</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $lesson->title }}</p>
                                        <p class="text-xs text-gray-500 flex items-center gap-3">
                                            @if($lesson->video_url)<span>▶ Video</span>@endif
                                            @if($lesson->attachment_path)<span>📎 Tài liệu</span>@endif
                                            @if($lesson->content)<span>📝 Nội dung</span>@endif
                                        </p>
                                    </div>
                                </div>
                                @if($canEdit)
                                    <div class="flex items-center gap-1 shrink-0">
                                        @unless($loop->first)
                                            <form method="POST" action="{{ route('instructor.lessons.move', $lesson) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="direction" value="up">
                                                <button type="submit" title="Lên trên" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200">↑</button>
                                            </form>
                                        @endunless
                                        @unless($loop->last)
                                            <form method="POST" action="{{ route('instructor.lessons.move', $lesson) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="direction" value="down">
                                                <button type="submit" title="Xuống dưới" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200">↓</button>
                                            </form>
                                        @endunless
                                        <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Sửa</a>
                                        <form method="POST" action="{{ route('instructor.lessons.destroy', $lesson) }}" onsubmit="return confirm('Xóa bài học này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50">Xóa</button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="px-5 py-4 text-sm text-gray-500">Chương này chưa có bài học.</li>
                        @endforelse

                        <!-- Quiz cuối chương -->
                        @foreach($quizzesByChapter->get($chapter->id, collect()) as $quiz)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 bg-violet-50/40">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-violet-100 text-violet-700 text-xs font-bold flex items-center justify-center shrink-0">Q</span>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $quiz->title }} <span class="text-xs text-gray-500">({{ $quiz->questions_count }} câu hỏi)</span></p>
                                </div>
                                <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-violet-700 hover:bg-violet-100 shrink-0">Quản lý câu hỏi</a>
                            </li>
                        @endforeach
                    </ul>

                    @if($canEdit)
                        <div class="px-5 py-3 border-t border-gray-100 flex flex-wrap gap-2">
                            <a href="{{ route('instructor.chapters.lessons.create', $chapter) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100">+ Thêm bài học</a>
                            <a href="{{ route('instructor.courses.quizzes.create', [$course, 'chapter_id' => $chapter->id]) }}" class="px-3 py-1.5 rounded-lg bg-violet-50 text-violet-700 text-xs font-semibold hover:bg-violet-100">+ Thêm quiz cho chương</a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white p-10 rounded-2xl shadow-sm border border-dashed border-gray-300 text-center text-gray-500">
                    Khóa học chưa có chương nào. Hãy thêm chương đầu tiên.
                </div>
            @endforelse

            @if($canEdit)
                <form method="POST" action="{{ route('instructor.courses.chapters.store', $course) }}" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="text" name="title" required maxlength="255" placeholder="Tên chương mới, VD: Chương 1: Giới thiệu"
                           class="flex-1 rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">+ Thêm chương</button>
                </form>
            @endif
        </div>

        <!-- Cột phải: danh sách quiz -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Quiz trắc nghiệm</h3>
                @if($canEdit)
                    <a href="{{ route('instructor.courses.quizzes.create', $course) }}" class="px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-semibold hover:bg-violet-700">+ Tạo quiz</a>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 divide-y divide-gray-100">
                @forelse($course->quizzes as $quiz)
                    <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="block px-5 py-4 hover:bg-gray-50">
                        <p class="text-sm font-semibold text-gray-900">{{ $quiz->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $quiz->chapter->title ?? 'Quiz chung của khóa' }} · {{ $quiz->questions_count }} câu hỏi
                        </p>
                    </a>
                @empty
                    <p class="px-5 py-6 text-sm text-gray-500 text-center">Chưa có quiz nào.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
