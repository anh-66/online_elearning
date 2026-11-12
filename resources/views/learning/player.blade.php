@extends('layouts.player')

@section('title', $currentLesson->title . ' - ' . $course->title)
@section('course_title', $course->title)
@section('back_url', route('courses.show', $course->slug))

@php
    $isCurrentCompleted = in_array($currentLesson->id, $completedLessonIds);

    // Chuyển đổi link YouTube sang link embed nếu là YouTube
    $videoUrl = $currentLesson->video_url;
    $embedUrl = null;
    if ($videoUrl) {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $videoUrl, $matches)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0';
        }
    }

    // Dọn dẹp nội dung: Nếu video đang phát ở khung chính trên cùng,
    // loại bỏ thẻ iframe/oembed video trùng lặp bên dưới để tránh hiện 2 lần
    $displayContent = $currentLesson->content ?? '';
    if ($embedUrl && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $videoUrl, $vidMatches)) {
        $videoId = $vidMatches[1];
        $displayContent = preg_replace('/<figure[^>]*class=["\'][^"\']*media[^"\']*["\'][\s\S]*?' . preg_quote($videoId, '/') . '[\s\S]*?<\/figure>/i', '', $displayContent);
        $displayContent = preg_replace('/<div[^>]*data-oembed-url=["\'][^"\']*' . preg_quote($videoId, '/') . '[^"\']*["\'][\s\S]*?<\/div>\s*<\/div>/i', '', $displayContent);
        $displayContent = preg_replace('/<iframe[^>]*src=["\'][^"\']*' . preg_quote($videoId, '/') . '[^"\']*["\'][^>]*>\s*<\/iframe>/i', '', $displayContent);
        $displayContent = trim($displayContent);
    }

    $allLessonsList = $course->chapters->flatMap->lessons;
    $totalLessonsCount = $allLessonsList->count();
    $completedCount = count($completedLessonIds);
    $commentCount = $currentLesson->comments()->count();

    // Helper dọn dẹp tiêu đề tránh bị lặp tiền tố "Chương 1: Chương 1:" hoặc "Bài 1: Bài 1:"
    $formatChapterTitle = function($rawTitle, $index) {
        $title = trim($rawTitle ?? '');
        if (preg_match('/^chương\s*\d+\s*[:.-]?\s*/ui', $title)) {
            return $title;
        }
        return 'Chương ' . ($index + 1) . ': ' . $title;
    };

    $formatLessonItemTitle = function($rawTitle, $order, $iteration) {
        $title = trim($rawTitle ?? '');
        if (preg_match('/^(?:bài\s*\d+\s*[:.-]?|\d+[\.\-\)]\s*)/ui', $title)) {
            return $title;
        }
        return ($order ?? $iteration) . '. ' . $title;
    };

    $formatMainHeadingTitle = function($rawTitle, $order) {
        $title = trim($rawTitle ?? '');
        if (preg_match('/^(?:bài\s*\d+\s*[:.-]?|\d+[\.\-\)]\s*)/ui', $title)) {
            return $title;
        }
        return 'Bài ' . ($order ?? 1) . ': ' . $title;
    };

    $mainChapterHeading = $currentLesson->chapter 
        ? $formatChapterTitle($currentLesson->chapter->title, ($currentLesson->chapter->order ? $currentLesson->chapter->order - 1 : 0))
        : '';
    $mainLessonHeading = $formatMainHeadingTitle($currentLesson->title, $currentLesson->order);
@endphp

@section('progress_text', $progressPercent . '% (' . $completedCount . '/' . $totalLessonsCount . ' bài)')
@section('progress_percent', $progressPercent)

@section('content')
<main x-data="{ sidebarOpen: true }" class="max-w-[1440px] w-full mx-auto p-4 sm:p-6 lg:p-8 flex flex-col lg:grid lg:grid-cols-12 gap-8 lg:gap-10 transition-all duration-300" data-sd-id="19">
    
    <!-- Left Content: Video Player & Info (Bung toàn màn hình khi sidebarOpen = false) -->
    <div :class="sidebarOpen ? 'lg:col-span-8' : 'lg:col-span-12'" class="w-full flex flex-col gap-8 min-w-0 transition-all duration-300" data-sd-id="20">
        
        <!-- Video Area & Header -->
        @if($videoUrl)
            <div class="relative aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl ring-1 ring-black/5" data-sd-id="21">
                @if($embedUrl)
                    <iframe src="{{ $embedUrl }}" class="w-full h-full block border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                @else
                    <video src="{{ $videoUrl }}" controls class="w-full h-full block object-contain">
                        Trình duyệt của bạn không hỗ trợ phát thẻ video.
                    </video>
                @endif

                <!-- Nút nổi mở lại mục lục khi đã thu gọn sidebar -->
                <button x-show="!sidebarOpen"
                        x-transition
                        @click="sidebarOpen = true"
                        type="button"
                        class="absolute top-4 right-4 z-20 flex items-center gap-2 px-3.5 py-2 rounded-xl bg-black/70 hover:bg-black/90 text-white backdrop-blur-md border border-white/20 text-xs sm:text-sm font-semibold shadow-lg transition"
                        title="Mở mục lục khóa học">
                    <svg class="w-4 h-4 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span>Mục lục</span>
                </button>
            </div>
        @else
            <!-- Thẻ bài đọc lý thuyết (Thay thế ô đen placeholder, giao diện sáng rõ ràng) -->
            <div class="relative p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-emerald-50/80 via-teal-50/40 to-blue-50/50 dark:from-[#161b22] dark:via-neutral-900 dark:to-neutral-900 border border-emerald-100/80 dark:border-neutral-800 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4 sm:gap-5">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[#019D91]/10 text-[#019D91] dark:text-[#00ddb3] flex items-center justify-center shrink-0 border border-[#019D91]/20">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#019D91] dark:text-[#00ddb3]">Bài đọc lý thuyết</span>
                            <span class="text-xs text-gray-300 dark:text-neutral-700">•</span>
                            <span class="text-xs text-gray-500 dark:text-neutral-400">Tài liệu học tập &amp; hướng dẫn chi tiết</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $currentLesson->title }}</h3>
                    </div>
                </div>

                <!-- Nút nổi mở lại mục lục khi đã thu gọn sidebar -->
                <button x-show="!sidebarOpen"
                        x-transition
                        @click="sidebarOpen = true"
                        type="button"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-neutral-800 text-gray-800 dark:text-white border border-gray-200 dark:border-neutral-700 text-xs sm:text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 transition shrink-0 ml-4"
                        title="Mở mục lục khóa học">
                    <svg class="w-4 h-4 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span>Mục lục</span>
                </button>
            </div>
        @endif

        <!-- Lesson Content: Tiêu đề bài học (chuẩn hóa không bị lặp chữ, font chữ to rõ ràng) -->
        <div class="flex flex-col gap-6" data-sd-id="30">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" data-sd-id="31">
                <div class="space-y-1.5 min-w-0">
                    @if(filled($mainChapterHeading))
                        <p class="text-xs sm:text-sm font-bold text-[#10B981] uppercase tracking-[0.15em]" data-sd-id="33">
                            {{ $mainChapterHeading }}
                        </p>
                    @endif
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white leading-snug sm:leading-[1.28]" data-sd-id="34">
                        {{ $mainLessonHeading }}
                    </h2>
                </div>

                {{-- Nút Đã hoàn thành bài học --}}
                <form action="{{ route('learning.complete', $currentLesson->id) }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" id="mark-complete-btn"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-neutral-800 text-white text-sm sm:text-base font-semibold rounded-xl hover:bg-black dark:hover:bg-neutral-700 transition-all shadow-lg shadow-black/5 active:scale-95" data-sd-id="35">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $isCurrentCompleted ? 'Đã hoàn thành' : 'Đánh dấu hoàn thành' }}</span>
                    </button>
                </form>
            </div>

            <div class="h-px bg-gray-100 dark:bg-neutral-800" data-sd-id="36"></div>

            {{-- Nội dung chi tiết bài học (Cỡ chữ to, thoáng đãng, dễ đọc) --}}
            @if(filled($displayContent))
                <div class="prose prose-base sm:prose-lg max-w-none text-gray-700 dark:text-gray-200 leading-relaxed text-base sm:text-lg" data-sd-id="37">
                    {!! $displayContent !!}
                </div>
            @endif

            {{-- Tài liệu đính kèm nếu có --}}
            @if($currentLesson->attachment_path)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                    <div class="flex items-center space-x-3 text-sm sm:text-base">
                        <svg class="w-5 h-5 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-gray-900 dark:text-white font-medium">Tài liệu đính kèm bài học</span>
                    </div>
                    <a href="{{ Storage::disk('public')->url($currentLesson->attachment_path) }}" target="_blank" download
                       class="px-4 py-2 text-sm font-semibold rounded-lg bg-white dark:bg-neutral-800 hover:bg-gray-100 dark:hover:bg-neutral-700 text-gray-800 dark:text-white border border-gray-200 dark:border-neutral-700 transition">
                        Tải về
                    </a>
                </div>
            @endif

            {{-- Điều hướng bài học Trước / Tiếp theo (Ngắn gọn, rõ ràng theo yêu cầu) --}}
            <div class="flex items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-neutral-800 text-sm">
                @if($prevLesson)
                    <a href="{{ route('learning.player', [$course->slug, $prevLesson->id]) }}"
                       class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-white dark:bg-[#161b22] hover:bg-gray-50 dark:hover:bg-[#21262d] text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-neutral-700 transition font-semibold shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <span>Bài trước</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextLesson)
                    <a href="{{ route('learning.player', [$course->slug, $nextLesson->id]) }}"
                       class="inline-flex items-center space-x-2 px-6 py-2.5 rounded-xl bg-[#10B981] hover:bg-[#059669] text-white font-bold transition shadow-sm">
                        <span>Bài tiếp theo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>

            {{-- Khung Thảo luận hỏi đáp (#qa) --}}
            <div id="qa" class="pt-6">
                @include('lessons.partials.comments', ['lesson' => $currentLesson])
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Curriculum (Có thể đóng/mở linh hoạt) -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-x-4"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-4"
         class="w-full lg:col-span-4 flex flex-col h-auto lg:h-[calc(100vh-160px)] lg:sticky lg:top-24" data-sd-id="39">
        <div class="bg-white dark:bg-[#161b22] rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm flex flex-col h-full" data-sd-id="40">
            
            <!-- Header Mục lục (Nút 3 gạch có thể bấm để thu gọn sidebar) -->
            <div class="p-5 sm:p-6 border-b border-gray-50 dark:border-neutral-800 flex items-center justify-between" data-sd-id="41">
                <button type="button"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-2.5 text-left group hover:opacity-80 transition"
                        title="Bấm để thu gọn mục lục bài học">
                    <div class="p-1.5 rounded-lg bg-[#10B981]/10 text-[#10B981] group-hover:bg-[#10B981]/20 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                    <span class="text-sm sm:text-base font-bold tracking-tight text-gray-900 dark:text-white" data-sd-id="43">NỘI DUNG KHÓA HỌC</span>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-400 dark:text-neutral-500" data-sd-id="44">
                        {{ $course->chapters->count() }} chương • {{ $totalLessonsCount }} bài
                    </span>
                    <button type="button"
                            @click="sidebarOpen = false"
                            class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-neutral-800 transition"
                            title="Thu gọn mục lục">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- List Bài học & Quiz -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-4" data-sd-id="45">
                @foreach($course->chapters as $cIndex => $chapter)
                    @php
                        $chapterTitleDisplay = $formatChapterTitle($chapter->title, $cIndex);
                    @endphp
                    <div class="mb-4" data-sd-id="chapter-{{ $chapter->id }}">
                        <!-- Chapter Header -->
                        <div class="px-3 py-2" data-sd-id="chap-header-{{ $chapter->id }}">
                            <h3 class="text-xs sm:text-sm font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider leading-snug">
                                {{ $chapterTitleDisplay }}
                            </h3>
                        </div>

                        <!-- Lessons in Chapter -->
                        <div class="space-y-1.5 mt-1">
                            @foreach($chapter->lessons as $lesson)
                                @php
                                    $isActive = $lesson->id === $currentLesson->id;
                                    $isCompleted = in_array($lesson->id, $completedLessonIds);
                                    $lessonTitleDisplay = $formatLessonItemTitle($lesson->title, $lesson->order, $loop->iteration);
                                @endphp

                                @if($isActive)
                                    <!-- Lesson Active -->
                                    <a href="{{ route('learning.player', [$course->slug, $lesson->id]) }}" id="lesson-{{ $lesson->id }}-link"
                                       class="flex items-start gap-3.5 p-3.5 rounded-xl bg-[#F0FDF4] dark:bg-[#10B981]/10 border border-[#DCFCE7] dark:border-[#10B981]/25 group" data-sd-id="lesson-active">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#10B981] flex items-center justify-center text-white text-xs mt-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm sm:text-base font-bold text-[#065F46] dark:text-[#34D399] leading-snug truncate">
                                                {{ $lessonTitleDisplay }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1 text-xs font-medium text-[#059669] dark:text-[#34D399]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $lesson->video_url ? 'Video' : 'Đọc tài liệu' }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <!-- Lesson Inactive -->
                                    <a href="{{ route('learning.player', [$course->slug, $lesson->id]) }}" id="lesson-{{ $lesson->id }}-link"
                                       class="flex items-start gap-3.5 p-3.5 rounded-xl hover:bg-gray-50 dark:hover:bg-[#21262d] transition-colors group" data-sd-id="lesson-inactive">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 {{ $isCompleted ? 'border-[#10B981] bg-[#10B981]/10 text-[#10B981]' : 'border-gray-200 dark:border-neutral-700 text-gray-400 dark:text-neutral-500' }} flex items-center justify-center text-xs font-bold mt-0.5">
                                            @if($isCompleted)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @else
                                                <span>{{ $lesson->order ?? $loop->iteration }}</span>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm sm:text-base font-semibold text-gray-700 dark:text-gray-300 leading-snug group-hover:text-gray-900 dark:group-hover:text-white truncate">
                                                {{ $lessonTitleDisplay }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1 text-xs font-medium text-gray-400 dark:text-neutral-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $lesson->video_url ? 'Video' : 'Đọc tài liệu' }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach

                            {{-- Quiz Card trong Chapter --}}
                            @foreach($chapter->quizzes as $quiz)
                                <div class="mx-1 mt-4 p-4 sm:p-5 rounded-xl bg-gray-900 dark:bg-black border border-gray-800 text-white relative overflow-hidden shadow-lg" data-sd-id="quiz-card-{{ $quiz->id }}">
                                    <!-- Background watermark graduation-cap -->
                                    <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.45 9L12 4.34 20.55 9 12 13.66 3.45 9zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                                    </svg>
                                    <div class="relative z-10" data-sd-id="quiz-content-{{ $quiz->id }}">
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-4 h-4 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-xs font-bold tracking-[0.1em] text-gray-400 uppercase">Kiểm tra kiến thức</span>
                                        </div>
                                        <h4 class="text-sm sm:text-base font-semibold mb-3 text-white line-clamp-2">{{ $quiz->title }}</h4>
                                        <a href="{{ route('quiz.show', $quiz) }}" id="start-quiz-btn"
                                           class="w-full py-2.5 bg-[#10B981] hover:bg-[#059669] text-white text-xs sm:text-sm font-bold rounded-lg transition-colors block text-center shadow-sm">
                                            LÀM BÀI KIỂM TRA
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Info -->
            <div class="p-5 sm:p-6 bg-gray-50/50 dark:bg-[#161b22]/50 rounded-b-2xl border-t border-gray-100 dark:border-neutral-800" data-sd-id="80">
                <div class="flex items-center justify-between mb-3" data-sd-id="81">
                    <span class="text-xs sm:text-sm text-gray-500 dark:text-neutral-400" data-sd-id="82">Mọi thắc mắc vui lòng đặt câu hỏi ở phần thảo luận.</span>
                </div>
                <button type="button" id="discussion-btn"
                        onclick="document.getElementById('qa')?.scrollIntoView({ behavior: 'smooth' })"
                        class="w-full py-3 border border-gray-200 dark:border-neutral-700 bg-white dark:bg-[#161b22] text-gray-700 dark:text-gray-300 text-xs sm:text-sm font-bold rounded-xl hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition-all shadow-sm" data-sd-id="83">
                    XEM THẢO LUẬN ({{ $commentCount }})
                </button>
            </div>
        </div>
    </div>
</main>
@endsection
