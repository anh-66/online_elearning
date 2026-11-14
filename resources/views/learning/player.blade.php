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

    // Dọn dẹp nội dung trùng video iframe
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

    // Helper dọn dẹp tiêu đề chương và bài học
    $formatChapterTitle = function($rawTitle, $index) {
        $title = trim($rawTitle ?? '');
        if (preg_match('/^chương\s*\d+\s*[:.-]?\s*/ui', $title)) {
            return $title;
        }
        return 'Chương ' . ($index + 1) . ': ' . $title;
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

@section('progress_percent', $progressPercent)

@section('content')
<div class="max-w-[1440px] w-full mx-auto flex flex-col lg:flex-row min-h-[calc(100vh-72px)] bg-white dark:bg-[#0d1117] transition-colors">
    
    {{-- Left Area: Video Player & Details --}}
    <div :class="sidebarOpen ? 'flex-1' : 'w-full'" class="p-6 sm:p-8 lg:p-10 overflow-y-auto border-r border-gray-100 dark:border-neutral-800 bg-white dark:bg-[#0d1117] min-w-0 transition-all duration-300">

        {{-- Học xong toàn bộ khóa: mời xem chứng chỉ --}}
        @if($courseCompleted)
            @php $justIssued = session('certificate_issued') && $certificate && session('certificate_issued') === $certificate->code; @endphp
            <div class="cert-banner {{ $justIssued ? 'cert-banner--new' : '' }}" role="status">
                @if($justIssued)
                    <span class="cert-banner__spark cert-banner__spark--1" aria-hidden="true"></span>
                    <span class="cert-banner__spark cert-banner__spark--2" aria-hidden="true"></span>
                    <span class="cert-banner__spark cert-banner__spark--3" aria-hidden="true"></span>
                @endif
                <span class="cert-banner__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="9" r="6" /><path d="M9.5 9l1.8 1.8L14.8 7.5" /><path d="M8.5 14.2L7 21l5-2.6L17 21l-1.5-6.8" /></svg>
                </span>
                <div class="cert-banner__text">
                    <p class="cert-banner__title">{{ $justIssued ? 'Chúc mừng! Bạn đã hoàn thành khóa học' : 'Bạn đã hoàn thành khóa học này' }}</p>
                    <p class="cert-banner__sub">Chứng chỉ của bạn đã sẵn sàng: có mã QR xác minh, tải PDF hoặc sao chép liên kết để gửi cho người khác.</p>
                </div>
                <a class="cert-banner__btn" href="{{ $certificate ? route('certificates.show', $certificate->code) : route('courses.certificate', $course->slug) }}">
                    Xem chứng chỉ
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                </a>
            </div>
        @endif

        {{-- Video Player Container --}}
        @if($videoUrl)
            <div class="aspect-video w-full bg-black rounded-xl overflow-hidden relative shadow-sm border border-gray-100 dark:border-neutral-800 group">
                @if($embedUrl)
                    <iframe src="{{ $embedUrl }}" class="w-full h-full block border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                @else
                    <video src="{{ $videoUrl }}" controls class="w-full h-full block object-contain">
                        Trình duyệt của bạn không hỗ trợ phát thẻ video.
                    </video>
                @endif

                {{-- Nút nổi mở lại mục lục khi đã thu gọn sidebar (phóng to khung học) --}}
                <button x-show="!sidebarOpen"
                        x-transition
                        @click="sidebarOpen = true"
                        type="button"
                        class="absolute top-4 right-4 z-20 flex items-center gap-2 px-3.5 py-2 rounded-full bg-black/80 hover:bg-black text-white dark:bg-white/90 dark:hover:bg-white dark:text-black backdrop-blur-md text-xs sm:text-sm font-semibold shadow-xl transition-all cursor-pointer group"
                        title="Mở lại danh sách bài học">
                    <iconify-icon icon="lucide:menu" class="text-base group-hover:rotate-90 transition-transform duration-200"></iconify-icon>
                    <span>Mục lục bài học</span>
                </button>
            </div>
        @else
            {{-- Theoretical Reading Card --}}
            <div class="p-8 sm:p-10 rounded-2xl bg-gray-50/80 dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 rounded-xl bg-black text-white dark:bg-white dark:text-black flex items-center justify-center shrink-0">
                        <iconify-icon icon="ph:book-open-text-light" class="text-2xl"></iconify-icon>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Bài đọc lý thuyết</p>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $currentLesson->title }}</h3>
                    </div>
                </div>

                {{-- Nút nổi mở lại mục lục khi đã thu gọn sidebar --}}
                <button x-show="!sidebarOpen"
                        x-transition
                        @click="sidebarOpen = true"
                        type="button"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-full bg-black hover:bg-gray-800 text-white dark:bg-white dark:hover:bg-gray-100 dark:text-black text-xs sm:text-sm font-semibold shadow-md transition-all shrink-0 ml-4 cursor-pointer"
                        title="Mở lại danh sách bài học">
                    <iconify-icon icon="lucide:menu" class="text-base"></iconify-icon>
                    <span>Mục lục bài học</span>
                </button>
            </div>
        @endif

        {{-- Lesson Header --}}
        <div class="mt-8 sm:mt-10">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="min-w-0 pr-2">
                    @if(filled($mainChapterHeading))
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">{{ $mainChapterHeading }}</p>
                    @endif
                    <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white leading-snug">
                        {{ $mainLessonHeading }}
                    </h2>
                </div>

                {{-- Mark Complete Button --}}
                <form action="{{ route('learning.complete', $currentLesson->id) }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" id="mark-complete-btn"
                            class="flex items-center gap-2 px-5 py-2.5 border rounded-full text-sm font-medium transition-all {{ $isCurrentCompleted ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white' : 'border-black dark:border-white text-gray-900 dark:text-white hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black' }}">
                        <iconify-icon icon="{{ $isCurrentCompleted ? 'lucide:check-circle-2' : 'lucide:check-circle' }}" class="text-lg"></iconify-icon>
                        <span>{{ $isCurrentCompleted ? 'Đã hoàn thành' : 'Đánh dấu hoàn thành' }}</span>
                    </button>
                </form>
            </div>
            
            {{-- Lesson Content Description --}}
            @if(filled($displayContent))
                <div class="mt-6 max-w-3xl prose prose-neutral dark:prose-invert text-gray-600 dark:text-gray-300 leading-relaxed text-sm sm:text-base">
                    {!! $displayContent !!}
                </div>
            @endif

            {{-- Attachment if any --}}
            @if($currentLesson->attachment_path)
                <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                    <div class="flex items-center gap-3 text-sm">
                        <iconify-icon icon="lucide:paperclip" class="text-lg text-gray-500"></iconify-icon>
                        <span class="font-medium text-gray-900 dark:text-white">Tài liệu đính kèm bài học</span>
                    </div>
                    <a href="{{ Storage::disk('public')->url($currentLesson->attachment_path) }}" target="_blank" download
                       class="px-4 py-2 text-xs font-semibold rounded-lg bg-white dark:bg-neutral-800 text-gray-800 dark:text-white border border-gray-200 dark:border-neutral-700 hover:bg-gray-100 transition">
                        Tải về
                    </a>
                </div>
            @endif

            {{-- Navigation Buttons --}}
            <div class="flex justify-between items-center mt-12 pt-8 border-t border-gray-100 dark:border-neutral-800">
                @if($prevLesson)
                    <a href="{{ route('learning.player', [$course->slug, $prevLesson->id]) }}" id="prev-lesson"
                       class="flex items-center gap-2 px-6 py-3 border border-gray-200 dark:border-neutral-700 rounded-full text-sm text-gray-600 dark:text-gray-300 hover:border-black dark:hover:border-white hover:text-black dark:hover:text-white transition-all font-medium">
                        <iconify-icon icon="lucide:chevron-left"></iconify-icon>
                        <span>Bài trước</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextLesson)
                    <a href="{{ route('learning.player', [$course->slug, $nextLesson->id]) }}" id="next-lesson"
                       class="flex items-center gap-2 px-6 py-3 bg-black dark:bg-white text-white dark:text-black border border-black dark:border-white rounded-full text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-200 transition-all">
                        <span>Bài tiếp theo</span>
                        <iconify-icon icon="lucide:chevron-right"></iconify-icon>
                    </a>
                @endif
            </div>
        </div>

        {{-- Q&A Section (#qa) --}}
        <section class="mt-16 sm:mt-20 pt-10 border-t border-gray-100 dark:border-neutral-800" id="qa">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-semibold flex items-center gap-2 text-gray-900 dark:text-white">
                    <iconify-icon icon="lucide:message-square" class="text-gray-400"></iconify-icon>
                    <span>Hỏi đáp &amp; Thảo luận</span>
                </h3>
                <span class="px-3 py-1 bg-gray-50 dark:bg-neutral-800 text-[11px] font-bold text-gray-500 dark:text-gray-400 rounded-full uppercase tracking-wider">
                    {{ $commentCount }} BÌNH LUẬN
                </span>
            </div>
            
            @include('lessons.partials.comments', ['lesson' => $currentLesson])
        </section>
    </div>

    {{-- Right Sidebar: Curriculum Navigation --}}
    <aside x-show="sidebarOpen"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 translate-x-8"
           x-transition:enter-end="opacity-100 translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 translate-x-0"
           x-transition:leave-end="opacity-0 translate-x-8"
           class="w-full lg:w-[380px] bg-[#fafafa] dark:bg-[#161b22] flex flex-col lg:h-[calc(100vh-72px)] lg:sticky lg:top-[72px] shrink-0 border-l border-gray-100 dark:border-neutral-800">
        
        {{-- Sidebar Header --}}
        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between bg-white dark:bg-[#161b22]">
            <button type="button"
                    @click="sidebarOpen = false"
                    class="flex items-center gap-2.5 text-left group hover:opacity-80 transition cursor-pointer"
                    title="Bấm để thu gọn mục lục (Phóng to khung học)">
                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-neutral-800 flex items-center justify-center text-gray-700 dark:text-gray-300 group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition">
                    <iconify-icon icon="lucide:menu" class="text-base"></iconify-icon>
                </div>
                <span class="font-semibold text-sm tracking-tight text-gray-900 dark:text-white">NỘI DUNG KHÓA HỌC</span>
            </button>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-gray-400 font-medium">{{ $course->chapters->count() }} chương • {{ $totalLessonsCount }} bài</span>
                <button type="button"
                        @click="sidebarOpen = false"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-neutral-800 transition cursor-pointer"
                        title="Thu gọn mục lục (Phóng to video)">
                    <iconify-icon icon="lucide:x" class="text-base"></iconify-icon>
                </button>
            </div>
        </div>

        {{-- Scrollable Chapter List --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @foreach($course->chapters as $chapterIndex => $chapter)
                <div class="{{ $loop->first ? 'mt-2' : 'mt-6' }}">
                    <div class="px-6 py-3">
                        <h4 class="text-[11px] font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-widest">
                            {{ $formatChapterTitle($chapter->title, $chapterIndex) }}
                        </h4>
                    </div>

                    <div class="space-y-0.5">
                        @foreach($chapter->lessons as $lessonIndex => $lesson)
                            @php
                                $isCurrent = $lesson->id === $currentLesson->id;
                                $isCompleted = in_array($lesson->id, $completedLessonIds);
                            @endphp
                            <a href="{{ route('learning.player', [$course->slug, $lesson->id]) }}"
                               class="flex gap-4 px-6 py-4 transition-colors group relative {{ $isCurrent ? 'bg-white dark:bg-neutral-900 border-y border-gray-100 dark:border-neutral-800' : 'hover:bg-gray-100 dark:hover:bg-neutral-800/60' }}">
                                
                                @if($isCurrent)
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-black dark:bg-white"></div>
                                @endif

                                {{-- Icon / Badge --}}
                                @if($isCompleted)
                                    <div class="w-6 h-6 rounded-full {{ $isCurrent ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-emerald-500 text-white' }} text-[10px] flex items-center justify-center shrink-0">
                                        <iconify-icon icon="lucide:check"></iconify-icon>
                                    </div>
                                @else
                                    <div class="w-6 h-6 rounded-full {{ $isCurrent ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-gray-400 group-hover:bg-white dark:group-hover:bg-neutral-700' }} text-[10px] font-bold flex items-center justify-center shrink-0">
                                        {{ $lessonIndex + 1 }}
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <p class="text-[13px] {{ $isCurrent ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-300 group-hover:text-black dark:group-hover:text-white' }} line-clamp-2">
                                        {{ $lesson->title }}
                                    </p>
                                    <p class="text-[11px] {{ $isCurrent ? 'text-gray-500 dark:text-neutral-400 font-medium' : 'text-gray-400' }} mt-1 flex items-center gap-1.5">
                                        <iconify-icon icon="{{ $lesson->video_url ? 'lucide:play-circle' : 'lucide:book-open' }}" class="{{ $isCurrent ? 'text-blue-500' : '' }}"></iconify-icon>
                                        <span>{{ $isCurrent ? 'Đang học • ' : '' }}{{ $lesson->video_url ? 'Video' : 'Bài đọc' }}</span>
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Knowledge Check Cards (Quizzes) --}}
            @php
                $allQuizzes = $course->quizzes ?? collect();
            @endphp
            @if($allQuizzes->isNotEmpty())
                @foreach($allQuizzes as $quiz)
                    <div class="mx-6 my-6">
                        <div class="p-6 bg-black dark:bg-neutral-900 rounded-2xl text-white relative overflow-hidden border border-neutral-800 shadow-md">
                            <div class="absolute -right-4 -bottom-4 opacity-10 rotate-12 pointer-events-none">
                                <iconify-icon icon="lucide:award" class="text-9xl"></iconify-icon>
                            </div>
                            <div class="relative z-10">
                                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-tighter mb-2">
                                    <iconify-icon icon="lucide:shield-check"></iconify-icon>
                                    Kiểm tra kiến thức
                                </div>
                                <h5 class="text-[15px] font-medium leading-tight mb-4 line-clamp-2">{{ $quiz->title }}</h5>
                                <a href="{{ route('quiz.show', $quiz) }}" class="w-full py-2.5 bg-white text-black text-xs font-bold rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-1">
                                    LÀM BÀI KIỂM TRA
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Footer of Sidebar: Link to Discussion --}}
        <div class="p-6 bg-white dark:bg-[#161b22] border-t border-gray-100 dark:border-neutral-800">
            <a href="#qa" id="view-all-discussion" class="w-full py-3 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm text-gray-500 dark:text-gray-400 font-medium hover:border-black dark:hover:border-white hover:text-black dark:hover:text-white transition-all flex items-center justify-center gap-2">
                <span>XEM THẢO LUẬN ({{ $commentCount }})</span>
                <iconify-icon icon="lucide:external-link" class="text-xs"></iconify-icon>
            </a>
        </div>
    </aside>

</div>
@endsection

@push('styles')
<style>
    .cert-banner { position: relative; overflow: hidden; display: flex; flex-wrap: wrap; align-items: center; gap: 14px 18px; margin-bottom: 24px; padding: 16px 18px; border-radius: 16px; color: #fff; background: linear-gradient(120deg, #0B4F4A 0%, #017F75 55%, #019D91 100%); box-shadow: 0 12px 28px -16px rgba(1, 79, 74, .7); }
    .cert-banner::after { content: ''; position: absolute; right: -40px; top: -60px; width: 180px; height: 180px; border-radius: 999px; background: radial-gradient(circle, rgba(227, 194, 111, .35), rgba(227, 194, 111, 0) 70%); pointer-events: none; }
    .cert-banner__icon { display: flex; align-items: center; justify-content: center; flex: none; width: 46px; height: 46px; border-radius: 999px; background: linear-gradient(135deg, #F1D88A, #B98A2F); color: #4A3510; box-shadow: inset 0 0 0 2px rgba(255, 255, 255, .35); }
    .cert-banner__icon svg { width: 26px; height: 26px; }
    .cert-banner__text { flex: 1; min-width: 220px; }
    .cert-banner__title { margin: 0; font-size: 16px; font-weight: 700; line-height: 1.35; }
    .cert-banner__sub { margin: 3px 0 0; font-size: 13.5px; line-height: 1.5; color: rgba(255, 255, 255, .82); }
    .cert-banner__btn { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 16px; border-radius: 10px; background: #fff; color: #0B4F4A; font-size: 14px; font-weight: 700; text-decoration: none; white-space: nowrap; transition: transform .15s, box-shadow .15s; }
    .cert-banner__btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px -6px rgba(0, 0, 0, .35); }
    .cert-banner__btn:focus-visible { outline: 3px solid #F1D88A; outline-offset: 2px; }
    .cert-banner__btn svg { width: 16px; height: 16px; }
    .cert-banner__spark { position: absolute; width: 8px; height: 8px; border-radius: 2px; background: #F1D88A; opacity: 0; animation: cert-spark 1.8s ease-out .2s 2; }
    .cert-banner__spark--1 { left: 30%; top: 70%; }
    .cert-banner__spark--2 { left: 55%; top: 75%; background: #fff; animation-delay: .45s; }
    .cert-banner__spark--3 { left: 78%; top: 70%; background: #7FE3D6; animation-delay: .7s; }
    .cert-banner--new { animation: cert-pop .5s cubic-bezier(.2, .9, .3, 1.3); }
    @keyframes cert-spark { 0% { opacity: 0; transform: translateY(0) rotate(0); } 20% { opacity: 1; } 100% { opacity: 0; transform: translateY(-70px) rotate(220deg); } }
    @keyframes cert-pop { from { transform: scale(.97); opacity: .4; } to { transform: scale(1); opacity: 1; } }
    @media (max-width: 520px) { .cert-banner__btn { width: 100%; justify-content: center; } }
    @media (prefers-reduced-motion: reduce) { .cert-banner--new, .cert-banner__spark { animation: none; } .cert-banner__btn { transition: none; } }
</style>
@endpush
