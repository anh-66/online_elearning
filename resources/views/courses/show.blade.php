@extends('layouts.public')

@section('title', $course->title)

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .font-inter,
        .font-inter * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .font-inter {
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
@endpush

@php
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $totalLessons = $course->chapters->sum(fn ($chapter) => $chapter->lessons->count());
    $galleryUrls = $course->images->pluck('image_path')
        ->when($course->thumbnail, fn ($paths) => $paths->prepend($course->thumbnail))
        ->unique()
        ->map(fn ($path) => Storage::disk('public')->url($path))
        ->values();
    $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
    
    $isCourseOwner = auth()->check() && auth()->id() === $course->instructor_id;
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp

@section('content')
<div class="min-h-screen bg-[#fcfcfc] dark:bg-[#0d1117] text-[#111827] dark:text-neutral-100 transition-colors pb-20 font-inter">

    {{-- Subtle Breadcrumb --}}
    <div class="max-w-7xl mx-auto px-6 md:px-12 pt-6 pb-2 flex items-center gap-2 text-xs sm:text-sm text-gray-400 dark:text-neutral-500">
        <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Trang chủ</a>
        <span>&rsaquo;</span>
        <a href="{{ route('courses.index') }}" class="hover:text-black dark:hover:text-white transition-colors">Khóa học</a>
        @if($course->category)
            <span>&rsaquo;</span>
            <a href="{{ route('courses.index', ['category' => $course->category_id]) }}" class="hover:text-black dark:hover:text-white transition-colors">{{ $course->category->name }}</a>
        @endif
    </div>

    {{-- Main Content Wrapper --}}
    <main class="max-w-7xl mx-auto px-6 md:px-12 pt-6 md:pt-10">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">
            
            {{-- Left Column: Details --}}
            <div class="flex-1 order-2 lg:order-1 min-w-0">
                
                {{-- Hero Info --}}
                <div class="mb-10 sm:mb-12">
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <span class="px-3 py-1 bg-gray-100 dark:bg-neutral-800 text-[11px] font-bold uppercase tracking-widest text-gray-600 dark:text-gray-300 rounded">
                            {{ $levelLabels[$course->level] ?? $course->level }}
                        </span>
                        <div class="flex items-center text-yellow-400 gap-1">
                            <iconify-icon icon="material-symbols:star" class="text-lg"></iconify-icon>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($averageRating, 1) }}</span>
                            <span class="text-sm text-gray-400">({{ $totalReviews }} đánh giá)</span>
                        </div>
                        <span class="text-gray-300 dark:text-neutral-700">•</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($course->enrollments_count) }} học viên</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight leading-[1.15] mb-6 text-gray-900 dark:text-white">
                        {{ $course->title }}
                    </h1>

                    @if($course->instructor)
                        <div class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-neutral-800 flex items-center justify-center overflow-hidden shrink-0">
                                @if($course->instructor->avatar_url)
                                    <img src="{{ $course->instructor->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $course->instructor->name }}" class="w-full h-full object-cover">
                                @else
                                    <iconify-icon icon="ph:user-bold" class="text-xl text-gray-500 dark:text-gray-400"></iconify-icon>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Giảng viên</p>
                                <a href="{{ route('instructors.show', $course->instructor->id) }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:underline decoration-black dark:decoration-white underline-offset-4">
                                    {{ $course->instructor->name }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Optional Gallery / Preview --}}
                @if($galleryUrls->count() > 1)
                    <div x-data="{ active: @js($galleryUrls->first()) }" class="mb-14 space-y-3">
                        <div class="rounded-2xl overflow-hidden aspect-[16/9] bg-gray-50 dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)]">
                            <img :src="active" src="{{ $galleryUrls->first() }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                            @foreach($galleryUrls as $url)
                                <button type="button" @click="active = @js($url)"
                                        class="rounded-xl overflow-hidden aspect-video bg-gray-50 dark:bg-[#161b22] border transition"
                                        :class="active === @js($url) ? 'border-black dark:border-white ring-2 ring-black/10 dark:ring-white/20' : 'border-gray-200 dark:border-neutral-800 hover:border-gray-300 dark:hover:border-neutral-700'">
                                    <img src="{{ $url }}" alt="Ảnh minh họa" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Description Section --}}
                <section class="mb-14 sm:mb-16">
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-100 dark:border-neutral-800">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Mô tả khóa học</h2>
                    </div>
                    <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 sm:p-8 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)]"
                         x-data="{
                             expanded: false,
                             canExpand: false,
                             maxHeight: 112,
                             fullHeight: 0,
                             init() {
                                 this.$nextTick(() => this.checkHeight());
                                 window.addEventListener('resize', () => this.checkHeight());
                             },
                             checkHeight() {
                                 if (this.$refs.content) {
                                     this.fullHeight = this.$refs.content.scrollHeight;
                                     this.canExpand = this.fullHeight > (this.maxHeight + 8);
                                 }
                             },
                             toggle() { this.expanded = !this.expanded; }
                         }">
                        @if(filled($course->description))
                            <div class="relative">
                                <div class="overflow-hidden transition-[max-height] duration-500 ease-[cubic-bezier(0.25,1,0.5,1)]"
                                     :style="canExpand ? (expanded ? `max-height: ${fullHeight + 30}px;` : `max-height: ${maxHeight}px;`) : ''">
                                    <div x-ref="content" class="rich-content text-gray-600 dark:text-gray-300 leading-relaxed text-sm sm:text-base">
                                        {!! $course->description !!}
                                    </div>
                                </div>
                                <div x-show="canExpand && !expanded" x-cloak
                                     class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-white dark:from-[#161b22] to-transparent">
                                </div>
                            </div>
                            <div x-show="canExpand" x-cloak class="mt-4 pt-2">
                                <button type="button" @click="toggle()"
                                        class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                    <span x-text="expanded ? 'Thu gọn bớt' : 'Xem thêm chi tiết & mục tiêu khóa học'"></span>
                                    <iconify-icon icon="lucide:chevron-down" class="text-base transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"></iconify-icon>
                                </button>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Giảng viên chưa cập nhật mô tả chi tiết.</p>
                        @endif
                    </div>
                </section>

                {{-- Curriculum Section --}}
                <section class="mb-14 sm:mb-16">
                    <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-100 dark:border-neutral-800">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Nội dung chi tiết</h2>
                        <span class="text-xs font-medium text-gray-400">{{ $course->chapters->count() }} chương • {{ $totalLessons }} bài học</span>
                    </div>

                    @if($course->chapters->isNotEmpty())
                        <div class="space-y-4" x-data="{ openChapters: [0] }">
                            @foreach($course->chapters as $chapterIndex => $chapter)
                                <div class="border border-gray-100 dark:border-neutral-800 rounded-2xl bg-white dark:bg-[#161b22] overflow-hidden shadow-[0_4px_20px_-2px_rgba(0,0,0,0.03)]">
                                    {{-- Chapter Header --}}
                                    <div @click="openChapters.includes({{ $chapterIndex }}) ? openChapters = openChapters.filter(i => i !== {{ $chapterIndex }}) : openChapters.push({{ $chapterIndex }})"
                                         class="p-5 sm:p-6 flex items-center justify-between bg-gray-50/50 dark:bg-neutral-900/50 hover:bg-gray-100/60 dark:hover:bg-neutral-800/60 transition-colors cursor-pointer select-none">
                                        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                                            <span class="w-8 h-8 rounded-lg bg-black text-white dark:bg-white dark:text-black flex items-center justify-center text-xs font-bold shrink-0">
                                                {{ $chapterIndex + 1 }}
                                            </span>
                                            <h3 class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white truncate">
                                                {{ $chapter->title }}
                                            </h3>
                                        </div>
                                        <div class="flex items-center gap-4 text-gray-400 shrink-0">
                                            <span class="text-xs font-medium">{{ $chapter->lessons->count() }} bài</span>
                                            <iconify-icon icon="lucide:chevron-down" 
                                                          class="text-base transition-transform duration-300"
                                                          :class="openChapters.includes({{ $chapterIndex }}) ? 'rotate-180' : ''">
                                            </iconify-icon>
                                        </div>
                                    </div>

                                    {{-- Chapter Content (Smooth AutoHeight) --}}
                                    <div class="overflow-hidden transition-[max-height,opacity] duration-350 ease-[cubic-bezier(0.2,0,0,1)]"
                                         :class="openChapters.includes({{ $chapterIndex }}) ? 'max-h-[1500px] opacity-100' : 'max-h-0 opacity-0 pointer-events-none'">
                                        <div class="p-2 divide-y divide-gray-50 dark:divide-neutral-800/80">
                                            @forelse($chapter->lessons as $lesson)
                                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-neutral-800/50 rounded-xl transition-colors cursor-pointer">
                                                    <div class="flex items-center gap-3.5 min-w-0 pr-3">
                                                        @if($lesson->video_url)
                                                            <iconify-icon icon="ph:video-camera-light" class="text-xl text-gray-400 shrink-0"></iconify-icon>
                                                        @else
                                                            <iconify-icon icon="ph:book-open-text-light" class="text-xl text-gray-400 shrink-0"></iconify-icon>
                                                        @endif
                                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $lesson->title }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 shrink-0">
                                                        {{ $lesson->video_url ? 'Video' : 'Bài đọc' }}
                                                    </span>
                                                </div>
                                            @empty
                                                <div class="p-4 text-xs text-gray-400">Chưa có bài học nào trong chương này.</div>
                                            @endforelse

                                            {{-- Quizzes in Chapter --}}
                                            @foreach($chapter->quizzes as $quiz)
                                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-neutral-800/50 rounded-xl transition-colors cursor-pointer bg-gray-50/30 dark:bg-neutral-900/30">
                                                    <div class="flex items-center gap-3.5 min-w-0 pr-3">
                                                        <iconify-icon icon="ph:notebook-light" class="text-xl text-emerald-600 dark:text-emerald-400 shrink-0"></iconify-icon>
                                                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">Quiz: {{ $quiz->title }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 shrink-0">
                                                        Kiểm tra
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-8 text-center text-sm text-gray-400">
                            Khóa học đang được cập nhật nội dung bài học.
                        </div>
                    @endif
                </section>

                {{-- Review Section --}}
                <section class="mb-16" id="reviews">
                    <h2 class="text-xl font-bold mb-8 pb-2 border-b border-gray-100 dark:border-neutral-800 text-gray-900 dark:text-white">
                        Đánh giá từ người học
                    </h2>

                    {{-- Rating Breakdown Minimalist Card --}}
                    <div class="flex flex-col md:flex-row gap-8 md:gap-12 items-center bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-8 sm:p-10 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)]">
                        <div class="text-center shrink-0">
                            <div class="text-6xl font-black mb-2 text-gray-900 dark:text-white tracking-tight">
                                {{ number_format($averageRating, 1) }}
                            </div>
                            <div class="flex justify-center gap-1 text-yellow-400 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <iconify-icon icon="material-symbols:star" class="text-lg {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-200 dark:text-neutral-700' }}"></iconify-icon>
                                @endfor
                            </div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">{{ $totalReviews }} đánh giá</p>
                        </div>
                        
                        {{-- 5 Bars Breakdown --}}
                        <div class="flex-1 w-full space-y-3">
                            @foreach([5, 4, 3, 2, 1] as $star)
                                @php
                                    $starCount = $ratingBreakdown[$star] ?? 0;
                                    $pct = $totalReviews > 0 ? round(($starCount / $totalReviews) * 100) : 0;
                                @endphp
                                <div class="flex items-center gap-4">
                                    <span class="text-xs font-medium w-12 text-gray-400 text-right">{{ $star }} sao</span>
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-neutral-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-black dark:bg-white transition-all duration-500 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium w-8 text-gray-400 text-right">{{ $pct }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Review Callout or Form --}}
                    @auth
                        @if($isEnrolled)
                            <div class="mt-8 bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 sm:p-8 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)]">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">
                                    {{ $userReview ? 'Cập nhật đánh giá của bạn' : 'Gửi đánh giá khóa học' }}
                                </h3>
                                <form action="{{ route('reviews.store', $course) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wider">Chọn số sao</label>
                                        <div class="flex items-center gap-2" x-data="{ rating: {{ old('rating', $userReview->rating ?? 5) }} }">
                                            <input type="hidden" name="rating" :value="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                                                    <iconify-icon icon="material-symbols:star"
                                                                  class="text-2xl transition-colors"
                                                                  :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-200 dark:text-neutral-700'">
                                                    </iconify-icon>
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wider">Nhận xét chi tiết</label>
                                        <textarea name="comment" rows="3" required
                                                  placeholder="Chia sẻ trải nghiệm học tập của bạn về khóa học này..."
                                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-neutral-700 bg-gray-50/50 dark:bg-neutral-900 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black dark:focus:ring-white transition">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="px-6 py-2.5 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm rounded-xl hover:bg-gray-800 dark:hover:bg-gray-200 transition">
                                            {{ $userReview ? 'Lưu thay đổi' : 'Gửi nhận xét' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @elseif(! $isCourseOwner)
                            <div class="mt-8 p-6 bg-gray-50 dark:bg-neutral-900 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Bạn cần sở hữu khóa học để có thể gửi đánh giá.</p>
                                <a href="{{ $course->price > 0 ? route('courses.checkout', $course->slug) : '#enroll-cta' }}" class="text-sm font-bold text-black dark:text-white flex items-center gap-1 group hover:underline underline-offset-4">
                                    {{ $course->price > 0 ? 'Mua khóa học ngay' : 'Ghi danh miễn phí ngay' }}
                                    <iconify-icon icon="lucide:arrow-right" class="group-hover:translate-x-1 transition-transform"></iconify-icon>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="mt-8 p-6 bg-gray-50 dark:bg-neutral-900 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Đăng nhập để chia sẻ đánh giá của bạn về khóa học.</p>
                            <a href="{{ route('login') }}" class="text-sm font-bold text-black dark:text-white flex items-center gap-1 hover:underline underline-offset-4">
                                Đăng nhập ngay
                                <iconify-icon icon="lucide:arrow-right"></iconify-icon>
                            </a>
                        </div>
                    @endauth

                    {{-- Review List --}}
                    @if($reviews->isNotEmpty())
                        <div class="mt-8 space-y-4">
                            @foreach($reviews as $review)
                                <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.02)]">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-black text-white dark:bg-white dark:text-black text-xs font-bold flex items-center justify-center shrink-0">
                                                {{ $review->user ? $review->user->initialLetter() : 'HV' }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $review->user?->name ?? 'Học viên ẩn danh' }}</p>
                                                <div class="flex items-center text-yellow-400 gap-0.5 text-xs">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <iconify-icon icon="material-symbols:star" class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200 dark:text-neutral-700' }}"></iconify-icon>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->locale('vi')->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $review->comment }}</p>

                                    {{-- Instructor Reply if exists --}}
                                    @if($review->instructor_reply)
                                        <div class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $review->instructor?->name ?? 'Giảng viên' }}</span>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-black text-white dark:bg-white dark:text-black">
                                                        Giảng viên
                                                    </span>
                                                </div>
                                                <span class="text-[11px] text-gray-400">
                                                    {{ $review->replied_at ? $review->replied_at->locale('vi')->diffForHumans() : '' }}
                                                </span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $review->instructor_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Pagination --}}
                            @if($reviews->hasPages())
                                <div class="pt-4">
                                    {{ $reviews->links() }}
                                </div>
                            @endif
                        </div>
                    @endif
                </section>
            </div>

            {{-- Right Column: Sticky Sidebar --}}
            <div class="w-full lg:w-[400px] order-1 lg:order-2 shrink-0">
                <div class="lg:sticky lg:top-28 space-y-6">
                    
                    {{-- Minimalist Course Card --}}
                    <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-3xl overflow-hidden shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)]">
                        
                        {{-- Course Thumbnail / Cover Image --}}
                        <div class="aspect-video bg-gray-100 dark:bg-neutral-800 relative flex items-center justify-center overflow-hidden">
                            @if($course->thumbnail)
                                <img src="{{ Storage::disk('public')->url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-gray-300 dark:text-neutral-700 bg-gray-50 dark:bg-neutral-900">
                                    <iconify-icon icon="lucide:image" class="text-4xl text-gray-300 dark:text-neutral-700"></iconify-icon>
                                    <span class="text-xs font-medium text-gray-400 dark:text-neutral-600">Ảnh bìa khóa học</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-6 sm:p-8">
                            {{-- Price Section: Chỉ hiển thị khi chưa mua / chưa sở hữu --}}
                            @if(! $isEnrolled && ! $isCourseOwner)
                                <div class="mb-6">
                                    <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-1.5">Mức học phí</p>
                                    <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight font-['Inter',sans-serif]">
                                        {{ $course->price == 0 ? 'Miễn phí' : number_format($course->price, 0, ',', '.') . ' đ' }}
                                    </h3>
                                </div>
                            @endif

                            {{-- Action Buttons --}}
                            <div class="space-y-3" id="enroll-cta">
                                @auth
                                    @if($isCourseOwner)
                                        <a href="{{ route('learning.player', $course->slug) }}"
                                           class="w-full flex items-center justify-center py-3.5 px-6 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm sm:text-base rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-2xs">
                                            Vào học ngay
                                        </a>
                                        @if(auth()->user()->isInstructor())
                                            <a href="{{ route('instructor.courses.show', $course) }}"
                                               class="w-full flex items-center justify-center py-3 px-6 bg-gray-100 text-gray-800 dark:bg-neutral-800 dark:text-neutral-200 font-semibold rounded-2xl hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors text-xs sm:text-sm">
                                                Quản lý khóa học (Studio)
                                            </a>
                                        @endif
                                    @elseif($isAdmin)
                                        <a href="{{ route('admin.courses.index') }}"
                                           class="w-full flex items-center justify-center py-3.5 px-6 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm sm:text-base rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-2xs">
                                            Quản lý khóa học (Admin)
                                        </a>
                                    @elseif($isEnrolled)
                                        <a href="{{ route('learning.player', $course->slug) }}"
                                           class="w-full flex items-center justify-center py-3.5 px-6 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm sm:text-base rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-2xs">
                                            Vào học ngay
                                        </a>
                                    @else
                                        @if($course->price > 0)
                                            <a href="{{ route('courses.checkout', $course->slug) }}"
                                               class="w-full flex items-center justify-center py-3.5 px-6 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm sm:text-base rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-2xs">
                                                Mua khóa học ngay - {{ number_format($course->price, 0, ',', '.') }} đ
                                            </a>
                                        @else
                                            <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center justify-center py-3.5 px-6 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm sm:text-base rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-2xs">
                                                    Ghi danh học miễn phí
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center py-3.5 px-6 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm sm:text-base rounded-2xl hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-2xs">
                                        {{ $course->price > 0 ? 'Mua khóa học ngay - ' . number_format($course->price, 0, ',', '.') . ' đ' : 'Đăng nhập để ghi danh miễn phí' }}
                                    </a>
                                @endauth

                                {{-- Wishlist Button (Bookmark with Bouncy Animation) --}}
                                @auth
                                    <div x-data="{
                                        pressed: {{ $isWishlisted ? 'true' : 'false' }},
                                        loading: false,
                                        animateIcon(isPressed) {
                                            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                                            const el = this.$refs.bookmarkSpan;
                                            if (!el) return;

                                            const keyframes = isPressed ? [
                                                { transform: 'scale(1)', offset: 0, easing: 'ease-out' },
                                                { transform: 'scale(1.25)', offset: 0.4, easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)' },
                                                { transform: 'scale(1)', offset: 1 }
                                            ] : [
                                                { transform: 'scale(1)', offset: 0, easing: 'ease-out' },
                                                { transform: 'scale(0.85)', offset: 0.4, easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)' },
                                                { transform: 'scale(1)', offset: 1 }
                                            ];

                                            el.animate(keyframes, {
                                                duration: 350,
                                                fill: 'none'
                                            });
                                        },
                                        async toggle() {
                                            if (this.loading) return;
                                            this.loading = true;
                                            const nextState = !this.pressed;
                                            this.pressed = nextState;
                                            this.animateIcon(nextState);

                                            try {
                                                const res = await fetch('{{ route('wishlist.toggle', $course->id) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json',
                                                        'X-Requested-With': 'XMLHttpRequest'
                                                    }
                                                });
                                                const data = await res.json();
                                                if (typeof data.wishlisted !== 'undefined' && data.wishlisted !== this.pressed) {
                                                    this.pressed = data.wishlisted;
                                                    this.animateIcon(this.pressed);
                                                }
                                            } catch(e) {
                                                this.pressed = !nextState;
                                                this.animateIcon(this.pressed);
                                            } finally {
                                                this.loading = false;
                                            }
                                        }
                                    }">
                                        <button type="button" @click="toggle()"
                                                :aria-pressed="pressed ? 'true' : 'false'"
                                                class="w-full flex items-center justify-center py-3.5 px-6 bg-white border border-gray-200 text-black dark:bg-[#161b22] dark:border-neutral-700 dark:text-white font-semibold rounded-2xl hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors gap-2 text-sm shadow-2xs">
                                            <span x-ref="bookmarkSpan" class="inline-flex items-center justify-center pointer-events-none">
                                                <svg class="size-4 stroke-[2] stroke-current transition-[fill,color] duration-200 ease-out"
                                                     :class="pressed ? 'fill-black text-black dark:fill-white dark:text-white' : 'fill-transparent text-gray-700 dark:text-gray-200'"
                                                     viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          :fill="pressed ? 'currentColor' : 'none'"
                                                          d="M6 3.5h12a1.5 1.5 0 0 1 1.5 1.5v16.327a.75.75 0 0 1-1.204.6l-5.328-4.043a1.5 1.5 0 0 0-1.836 0l-5.328 4.043a.75.75 0 0 1-1.204-.6V5A1.5 1.5 0 0 1 6 3.5Z"/>
                                                </svg>
                                            </span>
                                            <span x-text="pressed ? 'Đã thêm vào danh sách yêu thích' : 'Thêm vào danh sách yêu thích'"></span>
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="w-full flex items-center justify-center py-3.5 px-6 bg-white border border-gray-200 text-black dark:bg-[#161b22] dark:border-neutral-700 dark:text-white font-semibold rounded-2xl hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors gap-2 text-sm shadow-2xs">
                                        <svg class="size-4 stroke-[2] stroke-current fill-transparent text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" fill="none"
                                                  d="M6 3.5h12a1.5 1.5 0 0 1 1.5 1.5v16.327a.75.75 0 0 1-1.204.6l-5.328-4.043a1.5 1.5 0 0 0-1.836 0l-5.328 4.043a.75.75 0 0 1-1.204-.6V5A1.5 1.5 0 0 1 6 3.5Z"/>
                                        </svg>
                                        <span>Thêm vào danh sách yêu thích</span>
                                    </a>
                                @endauth

                                @if(! $isEnrolled && ! $isCourseOwner)
                                    <p class="text-[11px] text-center text-gray-400 dark:text-neutral-500 mt-3 font-normal">
                                        Thanh toán an toàn • Học trọn đời
                                    </p>
                                @endif
                            </div>

                            {{-- Metadata Properties --}}
                            <div class="mt-8 space-y-4 pt-6 border-t border-gray-100 dark:border-neutral-800">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-400 font-medium">Cấp độ</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $levelLabels[$course->level] ?? $course->level }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-400 font-medium">Danh mục</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $course->category->name ?? 'Chưa phân loại' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-400 font-medium">Tổng thời lượng</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $course->chapters->count() }} chương • {{ $totalLessons }} bài</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-400 font-medium">Số học viên</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($course->enrollments_count) }} người</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Small Instructor Card --}}
                    @if($course->instructor)
                        <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 flex items-center gap-4 shadow-[0_4px_20px_-2px_rgba(0,0,0,0.05)]">
                            <div class="w-12 h-12 rounded-full bg-black text-white dark:bg-white dark:text-black flex items-center justify-center font-bold text-base shrink-0">
                                {{ $course->instructor->initialLetter() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $course->instructor->name }}</h4>
                                <a href="{{ route('instructors.show', $course->instructor->id) }}" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-black dark:hover:text-white transition-colors block mt-0.5">
                                    Xem trang giảng viên ›
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </main>
</div>
@endsection
