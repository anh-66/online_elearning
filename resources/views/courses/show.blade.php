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

{{-- ===== UNIFIED MODERN SAAS COURSE LAYOUT (Clean 2-Column Grid) ===== --}}
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 transition-colors">

    {{-- Breadcrumb Navigation --}}
    <nav class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-gray-900 dark:hover:text-white transition">Trang chủ</a>
        <span aria-hidden="true">&rsaquo;</span>
        <a href="{{ route('courses.index') }}" class="hover:text-gray-900 dark:hover:text-white transition">Khóa học</a>
        @if($course->category)
            <span aria-hidden="true">&rsaquo;</span>
            <a href="{{ route('courses.index', ['category' => $course->category_id]) }}" class="hover:text-gray-900 dark:hover:text-white transition">{{ $course->category->name }}</a>
        @endif
    </nav>

    {{-- Main 2-Column Balanced Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

        {{-- ===== LEFT COLUMN: Header Info, Gallery, Description, Curriculum, Reviews (8 cols) ===== --}}
        <div class="lg:col-span-8 space-y-8 min-w-0">

            {{-- Course Title & Meta Header --}}
            <div class="space-y-3">
                @if($course->category)
                    <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#019D91] dark:text-[#02b3a5]">
                        {{ $course->category->name }}
                    </span>
                @endif

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-relaxed sm:leading-[1.7] lg:leading-[1.75]">
                    {{ $course->title }}
                </h1>

                {{-- Course Metadata Badges --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($averageRating, 1) }}</span>
                        <div class="flex items-center text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-gray-300 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                            @endfor
                        </div>
                        <a href="#reviews" class="hover:text-[#019D91] dark:hover:text-[#02b3a5]">({{ $totalReviews }} đánh giá)</a>
                    </div>
                    <span>&bull;</span>
                    <span>{{ number_format($course->enrollments_count) }} học viên</span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-gray-300 font-medium">
                        {{ $levelLabels[$course->level] ?? $course->level }}
                    </span>
                    @if($course->instructor)
                        <span>&bull;</span>
                        <span>Giảng viên:
                            <a href="{{ route('instructors.show', $course->instructor->id) }}" class="font-semibold text-gray-900 dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] underline ml-1">
                                {{ $course->instructor->name }}
                            </a>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Image Gallery --}}
            @if($galleryUrls->isNotEmpty())
                <div x-data="{ active: @js($galleryUrls->first()) }" class="space-y-3">
                    <div class="rounded-2xl overflow-hidden aspect-[16/10] bg-gray-50 dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 shadow-sm">
                        <img :src="active" src="{{ $galleryUrls->first() }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                    @if($galleryUrls->count() > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                            @foreach($galleryUrls as $url)
                                <button type="button" @click="active = @js($url)"
                                        class="rounded-xl overflow-hidden aspect-video bg-gray-50 dark:bg-[#161b22] border transition"
                                        :class="active === @js($url) ? 'border-[#019D91] ring-2 ring-[#019D91]/20' : 'border-gray-200 dark:border-neutral-800 hover:border-gray-300 dark:hover:border-neutral-700'">
                                    <img src="{{ $url }}" alt="Ảnh minh họa" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Section 1: Mô tả khóa học (Clean Soft Card) --}}
            <section class="bg-gray-50/70 dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 sm:p-8 transition-colors">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Mô tả khóa học</h2>
                @if(filled($course->description))
                    <div class="rich-content text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
                        {!! $course->description !!}
                    </div>
                @else
                    <p class="text-xs text-gray-400">Giảng viên chưa cập nhật mô tả chi tiết.</p>
                @endif
            </section>

            {{-- Section 2: Đề cương chi tiết (Sleek Clean Accordion) --}}
            <section class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 sm:p-8 transition-colors">
                <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100 dark:border-neutral-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Nội dung chi tiết</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $course->chapters->count() }} chương · {{ $totalLessons }} bài học</span>
                </div>

                @if($course->chapters->isNotEmpty())
                    <div class="space-y-3" x-data="{ openChapters: [0] }">
                        @foreach($course->chapters as $chapterIndex => $chapter)
                            <div class="border border-gray-100 dark:border-neutral-800 rounded-xl overflow-hidden bg-white dark:bg-[#0d1117]">
                                <button type="button"
                                    @click="openChapters.includes({{ $chapterIndex }}) ? openChapters = openChapters.filter(i => i !== {{ $chapterIndex }}) : openChapters.push({{ $chapterIndex }})"
                                    class="w-full flex items-center justify-between px-5 py-3.5 bg-gray-50 dark:bg-[#161b22] hover:bg-gray-100/80 dark:hover:bg-neutral-800 transition text-left">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <span class="w-6 h-6 rounded-full bg-white dark:bg-neutral-800 text-gray-900 dark:text-white border border-gray-200 dark:border-neutral-700 flex items-center justify-center text-xs font-semibold shrink-0">
                                            {{ $chapterIndex + 1 }}
                                        </span>
                                        <span class="font-semibold text-sm text-gray-900 dark:text-white truncate">{{ $chapter->title }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3 shrink-0">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $chapter->lessons->count() }} bài</span>
                                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                                             :class="openChapters.includes({{ $chapterIndex }}) ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="openChapters.includes({{ $chapterIndex }})" x-cloak>
                                    @forelse($chapter->lessons as $lesson)
                                        <div class="flex items-center space-x-3 px-5 py-3 border-t border-gray-100 dark:border-neutral-800 text-xs">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                              @if($lesson->video_url)
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                              @else
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                              @endif
                                            </svg>
                                            <span class="flex-1 text-gray-800 dark:text-gray-200 font-medium">{{ $lesson->title }}</span>
                                            <span class="text-[11px] text-gray-400">{{ $lesson->video_url ? 'Video' : 'Bài đọc' }}</span>
                                        </div>
                                    @empty
                                        <div class="px-5 py-3 text-xs text-gray-400 border-t border-gray-100 dark:border-neutral-800">Chưa có bài học nào trong chương này.</div>
                                    @endforelse

                                    @foreach($chapter->quizzes as $quiz)
                                        <div class="flex items-center space-x-3 px-5 py-3 border-t border-gray-100 dark:border-neutral-800 text-xs bg-gray-50/50 dark:bg-neutral-900/50">
                                            <svg class="w-4 h-4 text-[#019D91] dark:text-[#02b3a5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="flex-1 text-gray-800 dark:text-gray-200 font-semibold">Quiz: {{ $quiz->title }}</span>
                                            <span class="text-[11px] text-[#019D91] dark:text-[#02b3a5] font-medium">Kiểm tra kiến thức</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400">Chưa có nội dung đề cương.</p>
                @endif
            </section>

            {{-- Section 3: Đánh giá học viên (Clean Modern Review Card) --}}
            <section id="reviews" class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 sm:p-8 scroll-mt-24 transition-colors">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Đánh giá từ người học</h2>

                <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8 p-6 bg-gray-50 dark:bg-[#0d1117] rounded-2xl border border-gray-100 dark:border-neutral-800">
                    <div class="text-center sm:w-36 shrink-0">
                        <p class="text-5xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($averageRating, 1) }}</p>
                        <div class="flex items-center justify-center mt-2 space-x-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-gray-300 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-400 mt-1 font-medium">{{ $totalReviews }} đánh giá</p>
                    </div>

                    <div class="flex-1 space-y-2">
                        @foreach($ratingBreakdown as $star => $count)
                            @php
                                $percent = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-3 text-xs">
                                <span class="w-10 shrink-0 text-gray-500 dark:text-gray-400 font-medium">{{ $star }} sao</span>
                                <div class="flex-1 h-2 rounded-full bg-gray-200 dark:bg-neutral-800 overflow-hidden">
                                    <div class="h-full rounded-full bg-amber-400" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="w-10 shrink-0 text-right text-[11px] text-gray-400 font-medium">{{ $percent }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Khung gửi / chỉnh sửa đánh giá --}}
                <div class="mb-8 p-6 bg-gray-50 dark:bg-[#0d1117] rounded-2xl border border-gray-100 dark:border-neutral-800">
                    @auth
                        @if($userReview)
                            <div x-data="{ isEditing: false }">
                                <div x-show="!isEditing">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-semibold uppercase tracking-wider text-[#019D91] dark:text-[#02b3a5]">Đánh giá của bạn</span>
                                            <span class="text-[11px] text-gray-400">({{ $userReview->updated_at->locale('vi')->diffForHumans() }})</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="isEditing = true" class="text-xs text-[#019D91] dark:text-[#02b3a5] hover:underline font-medium">Chỉnh sửa</button>
                                            <form action="{{ route('reviews.destroy', $userReview) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:underline font-medium">Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $userReview->rating ? 'text-amber-400' : 'text-gray-300 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                        @endfor
                                        <span class="text-xs font-semibold ml-1 text-gray-900 dark:text-white">{{ $userReview->rating }}/5 sao</span>
                                    </div>
                                    @if(filled($userReview->comment))
                                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $userReview->comment }}</p>
                                    @endif
                                </div>

                                {{-- Form chỉnh sửa đánh giá --}}
                                <form x-show="isEditing" x-cloak action="{{ route('reviews.update', $userReview) }}" method="POST"
                                      x-data="{ currentRating: {{ $userReview->rating }}, hoverRating: 0 }" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Chỉnh sửa đánh giá của bạn</h3>
                                        <button type="button" @click="isEditing = false" class="text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white">Hủy</button>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Số sao</label>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" @click="currentRating = {{ $i }}" @mouseenter="hoverRating = {{ $i }}" @mouseleave="hoverRating = 0"
                                                        class="p-0.5 text-amber-400 focus:outline-none transition-transform hover:scale-110">
                                                    <svg class="w-6 h-6 transition-colors" :class="(hoverRating || currentRating) >= {{ $i }} ? 'text-amber-400 fill-current' : 'text-gray-300 dark:text-neutral-700 fill-current'" viewBox="0 0 20 20">
                                                        <path d="{{ $starPath }}"/>
                                                    </svg>
                                                </button>
                                            @endfor
                                            <input type="hidden" name="rating" :value="currentRating">
                                            <span class="text-xs font-semibold text-gray-900 dark:text-white ml-2" x-text="currentRating + ' sao'"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="review_edit_comment" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nhận xét (không bắt buộc)</label>
                                        <textarea id="review_edit_comment" name="comment" rows="3" maxlength="1000"
                                                  class="w-full text-sm rounded-xl border-gray-200 dark:border-neutral-700 bg-white dark:bg-[#161b22] text-gray-900 dark:text-white focus:border-[#019D91] focus:ring-[#019D91]">{{ old('comment', $userReview->comment) }}</textarea>
                                    </div>

                                    <div class="flex justify-end gap-2">
                                        <button type="button" @click="isEditing = false" class="text-xs px-4 py-2 border border-gray-200 dark:border-neutral-700 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100">Hủy</button>
                                        <button type="submit" class="text-xs px-5 py-2 font-semibold bg-[#019D91] text-white rounded-lg hover:bg-[#008b80]">Cập nhật đánh giá</button>
                                    </div>
                                </form>
                            </div>
                        @elseif(auth()->id() === $course->instructor_id)
                            <div class="flex items-center justify-between text-sm sm:text-base text-gray-500 dark:text-gray-400">
                                <span>Bạn là giảng viên phụ trách khóa học này. Bạn có thể phản hồi các đánh giá của học viên bên dưới.</span>
                            </div>
                        @elseif($isEnrolled)
                            <form action="{{ route('reviews.store', $course) }}" method="POST"
                                  x-data="{ currentRating: {{ old('rating', 5) }}, hoverRating: 0 }" class="space-y-4">
                                @csrf
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Viết đánh giá của bạn</h3>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bạn đánh giá khóa học này bao nhiêu sao?</label>
                                    <div class="flex items-center space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" @click="currentRating = {{ $i }}" @mouseenter="hoverRating = {{ $i }}" @mouseleave="hoverRating = 0"
                                                    class="p-0.5 text-amber-400 focus:outline-none transition-transform hover:scale-110">
                                                <svg class="w-6 h-6 transition-colors" :class="(hoverRating || currentRating) >= {{ $i }} ? 'text-amber-400 fill-current' : 'text-gray-300 dark:text-neutral-700 fill-current'" viewBox="0 0 20 20">
                                                    <path d="{{ $starPath }}"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" :value="currentRating">
                                        <span class="text-xs font-semibold text-gray-900 dark:text-white ml-2" x-text="currentRating + ' sao'"></span>
                                    </div>
                                    @error('rating')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="review_comment" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Chia sẻ cảm nhận chi tiết (không bắt buộc)</label>
                                    <textarea id="review_comment" name="comment" rows="3" maxlength="1000" placeholder="Khóa học có hữu ích với bạn không? Nội dung và bài giảng thế nào?..."
                                              class="w-full text-sm rounded-xl border-gray-200 dark:border-neutral-700 bg-white dark:bg-[#161b22] text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#019D91] focus:ring-[#019D91]">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="text-xs font-semibold px-6 py-2.5 bg-[#019D91] text-white rounded-lg hover:bg-[#008b80] transition shadow-sm">
                                        Gửi đánh giá
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="flex items-center justify-between text-sm sm:text-base text-gray-500 dark:text-gray-400">
                                <span>Bạn cần sở hữu khóa học để có thể gửi đánh giá.</span>
                                <a href="#enroll-cta" class="text-[#019D91] dark:text-[#02b3a5] hover:underline font-semibold">{{ $course->price > 0 ? 'Mua khóa học ngay →' : 'Ghi danh ngay →' }}</a>
                            </div>
                        @endif
                    @else
                        <div class="flex items-center justify-between text-sm sm:text-base text-gray-500 dark:text-gray-400">
                            <span>Đăng nhập để chia sẻ đánh giá của bạn về khóa học.</span>
                            <a href="{{ route('login') }}" class="text-[#019D91] dark:text-[#02b3a5] hover:underline font-semibold">Đăng nhập &rarr;</a>
                        </div>
                    @endauth
                </div>

                {{-- Danh sách đánh giá --}}
                <div class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse($reviews as $review)
                        <div class="py-6 first:pt-0 last:pb-0" x-data="{ showReplyForm: false, isEditingReply: false }">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-black text-white dark:bg-neutral-800 dark:text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-sm">
                                        {{ $review->user ? $review->user->initialLetter() : 'HV' }}
                                    </div>
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $review->user?->name ?? 'Học viên ẩn danh' }}</span>
                                </div>
                                <span class="text-xs text-gray-400">{{ $review->created_at->locale('vi')->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center space-x-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-300 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                @endfor
                            </div>
                            <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed">{{ $review->comment }}</p>

                            {{-- Phản hồi của Giảng viên nếu có --}}
                            @if($review->instructor_reply)
                                <div class="mt-3.5 ml-4 sm:ml-8 p-4 rounded-xl bg-gray-50 dark:bg-[#0d1117] border border-gray-100 dark:border-neutral-800 space-y-2">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-[#019D91] text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-sm">
                                                {{ $review->instructor ? $review->instructor->initialLetter() : 'GV' }}
                                            </div>
                                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $review->instructor?->name ?? 'Giảng viên' }}</span>
                                            <span class="text-[11px] uppercase font-bold px-2 py-0.5 rounded-full bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5] border border-[#019D91]/30">
                                                Giảng viên
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                {{ $review->replied_at ? $review->replied_at->locale('vi')->diffForHumans() : '' }}
                                            </span>
                                        </div>

                                        {{-- Thao tác Sửa / Xóa cho Giảng viên hoặc Admin --}}
                                        @can('reply', $review)
                                            <div class="flex items-center gap-2 text-xs sm:text-sm">
                                                <button type="button" @click="isEditingReply = !isEditingReply" class="text-gray-500 hover:text-[#019D91] dark:hover:text-[#02b3a5] font-medium transition">Sửa</button>
                                                <span class="text-gray-300 dark:text-neutral-700">·</span>
                                                <form action="{{ route('reviews.destroy-reply', $review) }}" method="POST"
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-500 hover:underline font-medium">Xóa</button>
                                                </form>
                                            </div>
                                        @endcan
                                    </div>

                                    {{-- Nội dung phản hồi --}}
                                    <div x-show="!isEditingReply">
                                        <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $review->instructor_reply }}</p>
                                    </div>

                                    {{-- Form Sửa Phản hồi --}}
                                    @can('reply', $review)
                                        <form x-show="isEditingReply" x-cloak action="{{ route('reviews.reply', $review) }}" method="POST" class="space-y-2 pt-1">
                                            @csrf
                                            <textarea name="instructor_reply" rows="2" required maxlength="2000"
                                                      class="w-full text-sm rounded-xl bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 text-gray-900 dark:text-white focus:border-[#019D91] focus:ring-[#019D91]">{{ old('instructor_reply', $review->instructor_reply) }}</textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="isEditingReply = false" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white">Hủy</button>
                                                <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-[#019D91] text-white hover:bg-[#008b80]">Lưu phản hồi</button>
                                            </div>
                                        </form>
                                    @endcan
                                </div>
                            @else
                                {{-- Nút & Form Phản hồi cho Giảng viên (khi chưa có phản hồi) --}}
                                @can('reply', $review)
                                    <div class="mt-2.5">
                                        <button type="button" x-show="!showReplyForm" @click="showReplyForm = true"
                                                class="inline-flex items-center gap-1.5 text-sm text-[#019D91] dark:text-[#02b3a5] font-semibold hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            <span>Phản hồi đánh giá này</span>
                                        </button>

                                        {{-- Form Phản hồi mới --}}
                                        <form x-show="showReplyForm" x-cloak action="{{ route('reviews.reply', $review) }}" method="POST"
                                              class="mt-2.5 p-4 rounded-xl bg-gray-50 dark:bg-[#0d1117] border border-gray-100 dark:border-neutral-800 space-y-2">
                                            @csrf
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">Phản hồi của giảng viên cho đánh giá của {{ $review->user->name ?? 'học viên' }}:</p>
                                            <textarea name="instructor_reply" rows="2" required maxlength="2000"
                                                      placeholder="Cảm ơn học viên hoặc giải đáp góp ý của học viên..."
                                                      class="w-full text-sm rounded-xl bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 text-gray-900 dark:text-white placeholder-gray-400 focus:border-[#019D91] focus:ring-[#019D91]">{{ old('instructor_reply') }}</textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="showReplyForm = false" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-900 dark:hover:text-white">Hủy</button>
                                                <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-[#019D91] text-white hover:bg-[#008b80]">Gửi phản hồi</button>
                                            </div>
                                        </form>
                                    </div>
                                @endcan
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Chưa có đánh giá nào cho khóa học này.</p>
                    @endforelse
                </div>

                @if($reviews->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $reviews->fragment('reviews')->links() }}
                    </div>
                @endif
            </section>
        </div>

        {{-- ===== RIGHT COLUMN: Action & Course Card (4 cols, Sticky natural top) ===== --}}
        <aside class="lg:col-span-4 sticky top-24 space-y-6">
            <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl overflow-hidden shadow-sm transition-colors">
                {{-- Preview Media --}}
                <div class="aspect-video overflow-hidden bg-gray-50 dark:bg-[#0d1117] border-b border-gray-100 dark:border-neutral-800">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-neutral-600">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    @php
                        $isCourseOwner = auth()->check() && auth()->id() === $course->instructor_id;
                        $isAdmin = auth()->check() && auth()->user()->isAdmin();
                    @endphp

                    {{-- Status or Price Display --}}
                    <div class="mb-5">
                        @if($isEnrolled)
                            <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Trạng thái khóa học</span>
                            <p class="text-2xl sm:text-3xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight flex items-center gap-2">
                                <svg class="w-7 h-7 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Đã sở hữu</span>
                            </p>
                        @elseif($isCourseOwner)
                            <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Quyền truy cập</span>
                            <p class="text-2xl sm:text-3xl font-bold text-[#019D91] dark:text-[#02b3a5] tracking-tight flex items-center gap-2">
                                <svg class="w-7 h-7 shrink-0 text-[#019D91] dark:text-[#02b3a5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                <span>Giảng viên</span>
                            </p>

                        @else
                            <span class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Mức học phí</span>
                            @if($course->price == 0)
                                <p class="text-3xl sm:text-4xl font-bold text-[#019D91] dark:text-[#02b3a5] tracking-tight">Miễn phí</p>
                            @else
                                <p class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">{{ number_format($course->price, 0, ',', '.') }} ₫</p>
                            @endif
                        @endif
                    </div>

                    {{-- Action CTA --}}
                    <div id="enroll-cta">
                    @auth
                        @if($isCourseOwner)
                            <div class="space-y-2.5 mb-3">
                                <a href="{{ route('learning.player', $course->slug) }}"
                                   class="block w-full text-center bg-[#019D91] hover:bg-[#008b80] text-white font-bold px-6 py-3.5 rounded-xl transition shadow-sm text-base">
                                    Vào phòng học (Xem trước)
                                </a>
                                @if(auth()->user()->isInstructor())
                                    <a href="{{ route('instructor.courses.show', $course) }}"
                                       class="block w-full text-center bg-gray-100 hover:bg-gray-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-gray-800 dark:text-neutral-200 font-semibold px-4 py-3 rounded-xl transition text-sm">
                                        Quản lý nội dung (Studio)
                                    </a>
                                @endif
                            </div>
                            <p class="text-sm text-center text-[#019D91] dark:text-[#02b3a5] font-semibold">✓ Bạn là giảng viên phụ trách khóa học này</p>
                        @elseif($isAdmin)
                            <div class="space-y-2.5 mb-3">
                                @if($isEnrolled)
                                    <a href="{{ route('learning.player', $course->slug) }}"
                                       class="block w-full text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3.5 rounded-xl transition shadow-sm text-base">
                                        Vào học ngay
                                    </a>
                                @elseif($course->price > 0)
                                    <a href="{{ route('courses.checkout', $course->slug) }}"
                                       class="btn-apple-primary w-full py-3.5 text-base font-bold rounded-xl shadow-md block text-center">
                                        Đăng ký mua khóa học ({{ number_format($course->price, 0, ',', '.') }} ₫)
                                    </a>
                                @else
                                    <a href="{{ route('learning.player', $course->slug) }}"
                                       class="block w-full text-center bg-[#019D91] hover:bg-[#008b80] text-white font-bold px-6 py-3.5 rounded-xl transition shadow-sm text-base">
                                        Vào phòng học (Miễn phí)
                                    </a>
                                @endif
                                <a href="{{ route('admin.courses.index') }}"
                                   class="block w-full text-center bg-gray-100 hover:bg-gray-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-gray-800 dark:text-neutral-200 font-semibold px-4 py-3 rounded-xl transition text-sm">
                                    Quản trị khóa học (Admin)
                                </a>
                            </div>
                            <p class="text-xs text-center text-purple-600 dark:text-purple-400 font-medium">Quyền Quản trị viên hệ thống</p>
                        @elseif($isEnrolled)
                            <a href="{{ route('learning.player', $course->slug) }}"
                               class="block w-full text-center bg-emerald-600 text-white font-bold px-6 py-3.5 rounded-xl hover:bg-emerald-700 transition mb-2 shadow-sm text-base">
                                Vào học ngay
                            </a>
                            <p class="text-xs text-center text-gray-500 dark:text-gray-400">Truy cập trọn đời · Mọi lúc, mọi nơi</p>
                        @else
                            @if($course->price > 0)
                                <a href="{{ route('courses.checkout', $course->slug) }}"
                                   class="btn-apple-primary w-full py-3.5 text-base font-bold rounded-xl shadow-md block text-center mb-2">
                                    Mua khóa học ngay · {{ number_format($course->price, 0, ',', '.') }} ₫
                                </a>
                                <p class="text-xs text-center text-gray-500 dark:text-gray-400">Thanh toán an toàn · Học trọn đời</p>
                            @else
                                <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-apple-primary w-full py-3.5 text-base font-bold rounded-xl shadow-md mb-2">
                                        Ghi danh học miễn phí
                                    </button>
                                </form>
                                <p class="text-xs text-center text-gray-500 dark:text-gray-400">Miễn phí 100% · Học trọn đời</p>
                            @endif
                        @endif
                    @else
                        @if($course->price > 0)
                            <a href="{{ route('login') }}" class="btn-apple-primary w-full py-3.5 text-base font-bold rounded-xl mb-2 text-center shadow-md block">
                                Đăng nhập để mua khóa học ({{ number_format($course->price, 0, ',', '.') }} ₫)
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-apple-primary w-full py-3.5 text-base font-bold rounded-xl mb-2 text-center shadow-md block">
                                Đăng nhập để ghi danh miễn phí
                            </a>
                        @endif
                        <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                            Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="text-[#019D91] dark:text-[#02b3a5] hover:underline font-semibold">Đăng ký ngay</a>
                        </p>
                    @endauth
                    </div>

                    @auth
                        @if(! $isCourseOwner)
                            <div
                                x-data="{
                                    pressed: {{ $isWishlisted ? 'true' : 'false' }},
                                    loading: false,
                                    animateIcon(isPressed) {
                                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                                        const el = this.$refs.iconSpan;
                                        if (!el) return;

                                        const keyframes = isPressed ? [
                                            { transform: 'scale(1)', offset: 0, easing: 'ease-out' },
                                            { transform: 'scale(1.2)', offset: 0.4, easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)' },
                                            { transform: 'scale(1)', offset: 1 }
                                        ] : [
                                            { transform: 'scale(1)', offset: 0, easing: 'ease-out' },
                                            { transform: 'scale(0.9)', offset: 0.4, easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)' },
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
                                            const response = await fetch('{{ route('wishlist.toggle', $course->id) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                }
                                            });

                                            if (!response.ok) {
                                                throw new Error('Network error');
                                            }

                                            const data = await response.json();
                                            if (typeof data.wishlisted !== 'undefined' && data.wishlisted !== this.pressed) {
                                                this.pressed = data.wishlisted;
                                                this.animateIcon(this.pressed);
                                            }
                                        } catch (e) {
                                            this.pressed = !nextState;
                                            this.animateIcon(this.pressed);
                                            console.error('Wishlist toggle error:', e);
                                        } finally {
                                            this.loading = false;
                                        }
                                    }
                                }"
                                class="mt-3"
                            >
                                <button
                                    type="button"
                                    data-slot="toggle"
                                    @click="toggle()"
                                    :aria-pressed="pressed ? 'true' : 'false'"
                                    :data-state="pressed ? 'on' : 'off'"
                                    :disabled="loading"
                                    aria-label="Thêm vào danh sách yêu thích"
                                    class="group/toggle inline-flex w-full items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium transition-all outline-none border-gray-200 hover:bg-gray-100 hover:text-gray-900 focus-visible:ring-[3px] focus-visible:ring-gray-300 dark:border-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-white dark:focus-visible:ring-neutral-700 disabled:pointer-events-none disabled:opacity-50"
                                    :class="pressed 
                                        ? 'bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-white' 
                                        : 'bg-transparent text-gray-700 dark:text-gray-200'"
                                >
                                    <span
                                        x-ref="iconSpan"
                                        class="inline-flex items-center justify-center pointer-events-none"
                                    >
                                        <svg
                                            class="size-4 stroke-[2] stroke-current transition-[fill,color] duration-200 ease-out pointer-events-none"
                                            :class="pressed ? 'fill-black text-black dark:fill-white dark:text-white' : 'fill-transparent text-gray-700 dark:text-gray-200'"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :fill="pressed ? 'currentColor' : 'none'"
                                                d="M6 3.5h12a1.5 1.5 0 0 1 1.5 1.5v16.327a.75.75 0 0 1-1.204.6l-5.328-4.043a1.5 1.5 0 0 0-1.836 0l-5.328 4.043a.75.75 0 0 1-1.204-.6V5A1.5 1.5 0 0 1 6 3.5Z"
                                            />
                                        </svg>
                                    </span>
                                    <span x-text="pressed ? 'Đã lưu vào danh sách yêu thích' : 'Thêm vào danh sách yêu thích'">Thêm vào danh sách yêu thích</span>
                                </button>
                            </div>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            data-slot="toggle"
                            aria-label="Thêm vào danh sách yêu thích"
                            class="group/toggle mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-700 transition-all hover:bg-gray-100 hover:text-gray-900 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-gray-300 dark:border-neutral-700 dark:text-gray-200 dark:hover:bg-neutral-800 dark:hover:text-white dark:focus-visible:ring-neutral-700"
                        >
                            <span class="inline-flex items-center justify-center pointer-events-none">
                                <svg
                                    class="size-4 stroke-[2] stroke-current fill-transparent text-gray-700 transition-[fill,color] duration-200 ease-out pointer-events-none dark:text-gray-200"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        fill="none"
                                        d="M6 3.5h12a1.5 1.5 0 0 1 1.5 1.5v16.327a.75.75 0 0 1-1.204.6l-5.328-4.043a1.5 1.5 0 0 0-1.836 0l-5.328 4.043a.75.75 0 0 1-1.204-.6V5A1.5 1.5 0 0 1 6 3.5Z"
                                    />
                                </svg>
                            </span>
                            <span>Thêm vào danh sách yêu thích</span>
                        </a>
                    @endauth

                    {{-- Summary Properties --}}
                    <dl class="mt-6 text-sm divide-y divide-gray-100 dark:divide-neutral-800">
                        <div class="flex justify-between items-center py-3">
                            <dt class="text-gray-500 dark:text-gray-400 font-medium">Cấp độ</dt>
                            <dd class="font-bold text-gray-900 dark:text-white">{{ $levelLabels[$course->level] ?? $course->level }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <dt class="text-gray-500 dark:text-gray-400 font-medium">Danh mục</dt>
                            <dd class="font-bold text-gray-900 dark:text-white">{{ $course->category->name ?? 'Chưa phân loại' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <dt class="text-gray-500 dark:text-gray-400 font-medium">Tổng thời lượng</dt>
                            <dd class="font-bold text-gray-900 dark:text-white">{{ $course->chapters->count() }} chương · {{ $totalLessons }} bài</dd>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <dt class="text-gray-500 dark:text-gray-400 font-medium">Số học viên</dt>
                            <dd class="font-bold text-gray-900 dark:text-white">{{ number_format($course->enrollments_count) }} người</dd>
                        </div>
                    </dl>

                    {{-- Instructor Info --}}
                    @if($course->instructor)
                        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-neutral-800">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-2.5">Giảng viên</span>
                            <a href="{{ route('instructors.show', $course->instructor->id) }}" class="flex items-center space-x-3 group">
                                <div class="w-11 h-11 rounded-full bg-black text-white dark:bg-neutral-800 dark:text-white font-bold text-sm flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                                    @if($course->instructor->avatar_url)
                                        <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full">{{ $course->instructor->initialLetter() }}</span>
                                    @else
                                        <span>{{ $course->instructor->initialLetter() }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-sm sm:text-base text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition">{{ $course->instructor->name }}</p>
                                    <p class="text-xs text-gray-400 font-medium">Xem trang giảng viên &rsaquo;</p>
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
