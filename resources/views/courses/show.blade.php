@extends('layouts.public')

@section('title', $course->title)

@php
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $totalLessons = $course->chapters->sum(fn ($chapter) => $chapter->lessons->count());
    $galleryUrls = $course->images->pluck('image_path')
        ->when($course->thumbnail, fn ($paths) => $paths->prepend($course->thumbnail))
        ->unique()
        ->map(fn ($path) => asset('storage/'.$path))
        ->values();
    $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
@endphp

@section('content')

{{-- ===== COURSE HEADER (GitHub Dark Compatible Header) ===== --}}
<section class="bg-white dark:bg-[#0d1117] border-b border-[#E5E5EA] dark:border-[#30363d] py-10 sm:py-14 transition-colors">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12">
        {{-- Breadcrumb --}}
        <nav class="flex flex-wrap items-center gap-2 text-xs text-[#86868B] dark:text-[#8b949e] mb-4">
            <a href="{{ route('home') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Trang chủ</a>
            <span aria-hidden="true">&rsaquo;</span>
            <a href="{{ route('courses.index') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Khóa học</a>
            @if($course->category)
                <span aria-hidden="true">&rsaquo;</span>
                <a href="{{ route('courses.index', ['category' => $course->category_id]) }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">{{ $course->category->name }}</a>
            @endif
        </nav>

        <div class="lg:pr-[24rem]">
            {{-- Category kicker --}}
            @if($course->category)
                <p class="text-xs font-semibold uppercase tracking-wider text-[#019D91] dark:text-[#02b3a5] mb-2">
                    {{ $course->category->name }}
                </p>
            @endif

            {{-- Title --}}
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-[#1D1D1F] dark:text-[#f0f6fc] leading-[1.12]">
                {{ $course->title }}
            </h1>

            {{-- Course Metadata Badges --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-5 text-xs text-[#86868B] dark:text-[#8b949e]">
                <div class="flex items-center gap-1.5">
                    <span class="font-semibold text-[#1D1D1F] dark:text-[#f0f6fc]">{{ number_format($averageRating, 1) }}</span>
                    <div class="flex items-center text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-[#D2D2D7] dark:text-[#30363d]' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                        @endfor
                    </div>
                    <a href="#reviews" class="hover:text-[#019D91] dark:hover:text-[#02b3a5]">({{ $totalReviews }} đánh giá)</a>
                </div>
                <span>&bull;</span>
                <span>{{ number_format($course->enrollments_count) }} học viên</span>
                <span>&bull;</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-[#F5F5F7] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] border border-[#E5E5EA] dark:border-[#30363d] font-medium">
                    {{ $levelLabels[$course->level] ?? $course->level }}
                </span>
            </div>

            @if($course->instructor)
                <p class="mt-4 text-xs text-[#86868B] dark:text-[#8b949e]">
                    Giảng viên phụ trách:
                    <a href="{{ route('instructors.show', $course->instructor->id) }}" class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc] hover:text-[#019D91] dark:hover:text-[#02b3a5] underline ml-1">
                        {{ $course->instructor->name }}
                    </a>
                </p>
            @endif
        </div>
    </div>
</section>

{{-- ===== COURSE BODY & SIDEBAR ===== --}}
<div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12 py-10">
    <div class="flex flex-col-reverse lg:flex-row gap-8">

        {{-- ===== LEFT MAIN CONTENT ===== --}}
        <div class="flex-1 min-w-0 space-y-12">

            {{-- Image Gallery --}}
            @if($galleryUrls->isNotEmpty())
                <div x-data="{ active: @js($galleryUrls->first()) }">
                    <div class="rounded-3xl overflow-hidden aspect-[16/10] bg-[#F5F5F7] dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d]">
                        <img :src="active" src="{{ $galleryUrls->first() }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                    @if($galleryUrls->count() > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mt-3">
                            @foreach($galleryUrls as $url)
                                <button type="button" @click="active = @js($url)"
                                        class="rounded-xl overflow-hidden aspect-video bg-[#F5F5F7] dark:bg-[#161b22] border transition"
                                        :class="active === @js($url) ? 'border-[#019D91] ring-2 ring-[#019D91]/20' : 'border-[#E5E5EA] dark:border-[#30363d] hover:border-black/30 dark:hover:border-[#8b949e]'">
                                    <img src="{{ $url }}" alt="Ảnh minh họa" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Mô tả khóa học --}}
            <section class="bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl p-6 sm:p-8 transition-colors">
                <h2 class="text-xl font-semibold tracking-tight text-[#1D1D1F] dark:text-[#f0f6fc] mb-4">Mô tả khóa học</h2>
                @if(filled($course->description))
                    <div class="rich-content">
                        {!! $course->description !!}
                    </div>
                @else
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e]">Giảng viên chưa cập nhật mô tả chi tiết.</p>
                @endif
            </section>

            {{-- Đề cương khóa học --}}
            <section class="bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl p-6 sm:p-8 transition-colors">
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-[#E5E5EA] dark:border-[#30363d]">
                    <h2 class="text-xl font-semibold tracking-tight text-[#1D1D1F] dark:text-[#f0f6fc]">Nội dung chi tiết</h2>
                    <span class="text-xs text-[#86868B] dark:text-[#8b949e]">{{ $course->chapters->count() }} chương · {{ $totalLessons }} bài học</span>
                </div>

                @if($course->chapters->isNotEmpty())
                    <div class="space-y-3" x-data="{ openChapters: [0] }">
                        @foreach($course->chapters as $chapterIndex => $chapter)
                            <div class="border border-[#E5E5EA] dark:border-[#30363d] rounded-2xl overflow-hidden">
                                <button type="button"
                                    @click="openChapters.includes({{ $chapterIndex }}) ? openChapters = openChapters.filter(i => i !== {{ $chapterIndex }}) : openChapters.push({{ $chapterIndex }})"
                                    class="w-full flex items-center justify-between px-5 py-3.5 bg-[#F5F5F7] dark:bg-[#0d1117] hover:bg-[#EAEAEA] dark:hover:bg-[#21262d] transition text-left">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <span class="w-6 h-6 rounded-full bg-white dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#f0f6fc] border border-black/10 dark:border-[#30363d] flex items-center justify-center text-xs font-semibold shrink-0">
                                            {{ $chapterIndex + 1 }}
                                        </span>
                                        <span class="font-medium text-sm text-[#1D1D1F] dark:text-[#f0f6fc] truncate">{{ $chapter->title }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3 shrink-0">
                                        <span class="text-xs text-[#86868B] dark:text-[#8b949e]">{{ $chapter->lessons->count() }} bài</span>
                                        <svg class="w-4 h-4 text-[#86868B] dark:text-[#8b949e] transition-transform duration-200"
                                             :class="openChapters.includes({{ $chapterIndex }}) ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="openChapters.includes({{ $chapterIndex }})" x-cloak>
                                    @forelse($chapter->lessons as $lesson)
                                        <div class="flex items-center space-x-3 px-5 py-3 border-t border-[#E5E5EA] dark:border-[#30363d] text-xs">
                                            <svg class="w-4 h-4 text-[#86868B] dark:text-[#8b949e] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($lesson->video_url)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                @endif
                                            </svg>
                                            <span class="flex-1 text-[#1D1D1F] dark:text-[#e6edf3] font-normal">{{ $lesson->title }}</span>
                                            <span class="text-[11px] text-[#86868B] dark:text-[#8b949e]">{{ $lesson->video_url ? 'Video' : 'Bài đọc' }}</span>
                                        </div>
                                    @empty
                                        <div class="px-5 py-3 text-xs text-[#86868B] dark:text-[#8b949e] border-t border-[#E5E5EA] dark:border-[#30363d]">Chưa có bài học nào trong chương này.</div>
                                    @endforelse

                                    @foreach($chapter->quizzes as $quiz)
                                        <div class="flex items-center space-x-3 px-5 py-3 border-t border-[#E5E5EA] dark:border-[#30363d] text-xs bg-[#F5F5F7]/50 dark:bg-[#0d1117]/50">
                                            <svg class="w-4 h-4 text-[#019D91] dark:text-[#02b3a5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="flex-1 text-[#1D1D1F] dark:text-[#f0f6fc] font-medium">Quiz: {{ $quiz->title }}</span>
                                            <span class="text-[11px] text-[#019D91] dark:text-[#02b3a5]">Kiểm tra kiến thức</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e]">Chưa có nội dung đề cương.</p>
                @endif
            </section>

            {{-- Đánh giá học viên --}}
            <section id="reviews" class="bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl p-6 sm:p-8 scroll-mt-24 transition-colors">
                <h2 class="text-xl font-semibold tracking-tight text-[#1D1D1F] dark:text-[#f0f6fc] mb-6">Đánh giá từ người học</h2>

                <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8 p-5 bg-[#F5F5F7] dark:bg-[#0d1117] rounded-2xl border border-[#E5E5EA] dark:border-[#30363d]">
                    <div class="text-center sm:w-36 shrink-0">
                        <p class="text-5xl font-semibold text-[#1D1D1F] dark:text-[#f0f6fc] tracking-tight">{{ number_format($averageRating, 1) }}</p>
                        <div class="flex items-center justify-center mt-1 space-x-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-[#D2D2D7] dark:text-[#30363d]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs text-[#86868B] dark:text-[#8b949e] mt-1">{{ $totalReviews }} đánh giá</p>
                    </div>

                    <div class="flex-1 space-y-1.5">
                        @foreach($ratingBreakdown as $star => $count)
                            @php($percent = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="w-10 shrink-0 text-[#86868B] dark:text-[#8b949e]">{{ $star }} sao</span>
                                <div class="flex-1 h-2 rounded-full bg-[#E5E5EA] dark:bg-[#21262d] overflow-hidden">
                                    <div class="h-full rounded-full bg-amber-400" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="w-10 shrink-0 text-right text-[11px] text-[#86868B] dark:text-[#8b949e]">{{ $percent }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @forelse($reviews as $review)
                    <div class="py-5 border-b border-[#E5E5EA] dark:border-[#30363d] last:border-0">
                        <div class="flex items-center justify-between gap-3 mb-1">
                            <span class="font-semibold text-sm text-[#1D1D1F] dark:text-[#f0f6fc]">{{ $review->user->name ?? 'Người dùng' }}</span>
                            <span class="text-[11px] text-[#86868B] dark:text-[#8b949e]">{{ $review->created_at->locale('vi')->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center space-x-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-amber-400' : 'text-[#D2D2D7] dark:text-[#30363d]' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs text-[#1D1D1F] dark:text-[#c9d1d9] leading-relaxed">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e] text-center py-6">Chưa có đánh giá nào cho khóa học này.</p>
                @endforelse

                @if($reviews->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $reviews->fragment('reviews')->links() }}
                    </div>
                @endif
            </section>
        </div>

        {{-- ===== RIGHT SIDEBAR (GitHub Dark Bordered Floating Card) ===== --}}
        <aside class="w-full lg:w-80 shrink-0 lg:-mt-52">
            <div class="lg:sticky lg:top-24 bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl overflow-hidden shadow-xl shadow-black/[0.04] transition-colors">
                {{-- Preview Media --}}
                <div class="aspect-video overflow-hidden bg-[#F5F5F7] dark:bg-[#0d1117] border-b border-[#E5E5EA] dark:border-[#30363d]">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-[#86868B] dark:text-[#8b949e]">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    {{-- Price Display --}}
                    <div class="mb-5">
                        <span class="text-xs uppercase tracking-wider text-[#86868B] dark:text-[#8b949e] block mb-1">Mức học phí</span>
                        @if($course->price == 0)
                            <p class="text-3xl font-semibold text-[#019D91] dark:text-[#02b3a5] tracking-tight">Miễn phí</p>
                        @else
                            <p class="text-3xl font-semibold text-[#1D1D1F] dark:text-[#f0f6fc] tracking-tight">{{ number_format($course->price, 0, ',', '.') }} ₫</p>
                        @endif
                    </div>

                    {{-- Action CTA --}}
                    @auth
                        @if($isEnrolled)
<a href="{{ url('/courses/' . $course->id . '/learn') }}"
                               class="block w-full text-center bg-emerald-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-emerald-700 transition mb-3">
                                Vào học ngay
                            </a>
                            <p class="text-xs text-center text-emerald-600 font-medium">✓ Bạn đã ghi danh khóa học này</p>
                        @else
                            <form method="POST" action="{{ url('/courses/' . $course->id . '/enroll') }}">
                                @csrf
                                <button type="submit" class="btn-apple-primary w-full py-2.5 text-sm">
                                    Ghi danh học ngay
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="btn-apple-secondary w-full py-2.5 text-sm cursor-not-allowed opacity-60">
                                Ghi danh sắp mở
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-apple-primary w-full py-2.5 text-sm mb-2 text-center">
                            Đăng nhập để ghi danh
                        </a>
                        <p class="text-[11px] text-center text-[#86868B] dark:text-[#8b949e]">
                            Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="text-[#019D91] dark:text-[#02b3a5] hover:underline font-medium">Đăng ký ngay</a>
                        </p>
                    @endauth

                    {{-- Summary Properties --}}
                    <dl class="mt-6 text-xs divide-y divide-[#E5E5EA] dark:divide-[#30363d]">
                        <div class="flex justify-between items-center py-2.5">
                            <dt class="text-[#86868B] dark:text-[#8b949e]">Cấp độ</dt>
                            <dd class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc]">{{ $levelLabels[$course->level] ?? $course->level }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2.5">
                            <dt class="text-[#86868B] dark:text-[#8b949e]">Danh mục</dt>
                            <dd class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc]">{{ $course->category->name ?? 'Chưa phân loại' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2.5">
                            <dt class="text-[#86868B] dark:text-[#8b949e]">Tổng thời lượng</dt>
                            <dd class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc]">{{ $course->chapters->count() }} chương · {{ $totalLessons }} bài</dd>
                        </div>
                        <div class="flex justify-between items-center py-2.5">
                            <dt class="text-[#86868B] dark:text-[#8b949e]">Số học viên</dt>
                            <dd class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc]">{{ number_format($course->enrollments_count) }} người</dd>
                        </div>
                    </dl>

                    {{-- Instructor Info --}}
                    @if($course->instructor)
                        <div class="mt-6 pt-5 border-t border-[#E5E5EA] dark:border-[#30363d]">
                            <span class="text-[11px] uppercase tracking-wider text-[#86868B] dark:text-[#8b949e] block mb-2">Giảng viên</span>
                            <a href="{{ route('instructors.show', $course->instructor->id) }}" class="flex items-center space-x-3 group">
                                <div class="w-9 h-9 rounded-full bg-black text-white dark:bg-black dark:text-white font-semibold text-xs flex items-center justify-center overflow-hidden shrink-0 border border-transparent dark:border-[#30363d]">
                                    @if($course->instructor->avatar_url)
                                        <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full">{{ $course->instructor->initialLetter() }}</span>
                                    @else
                                        <span>{{ $course->instructor->initialLetter() }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-xs text-[#1D1D1F] dark:text-[#f0f6fc] group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition">{{ $course->instructor->name }}</p>
                                    <p class="text-[10px] text-[#86868B] dark:text-[#8b949e]">Xem trang giảng viên &rsaquo;</p>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </aside>

    </div>
</div>

@endsection
