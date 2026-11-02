@extends('layouts.public')

@section('title', $course->title)

@php
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $levelClasses = ['beginner' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300', 'intermediate' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300', 'advanced' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'];
    $totalLessons = $course->chapters->sum(fn ($chapter) => $chapter->lessons->count());
    $galleryUrls = $course->images->pluck('image_path')
        ->when($course->thumbnail, fn ($paths) => $paths->prepend($course->thumbnail))
        ->unique()
        ->map(fn ($path) => asset('storage/'.$path))
        ->values();
    $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
@endphp

@section('content')
{{-- ===== HERO ===== --}}
<section class="bg-gradient-to-br from-brand-50 via-white to-brand-100/60 dark:from-neutral-900 dark:via-neutral-950 dark:to-brand-950/40 border-b border-brand-100 text-neutral-900 dark:border-neutral-800 dark:text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400 mb-5">
            <a href="{{ route('home') }}" class="hover:text-brand-700 dark:hover:text-brand-400 transition">Trang chủ</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('courses.index') }}" class="hover:text-brand-700 dark:hover:text-brand-400 transition">Khóa học</a>
            @if($course->category)
                <span aria-hidden="true">/</span>
                <a href="{{ route('courses.index', ['category' => $course->category_id]) }}" class="hover:text-brand-700 dark:hover:text-brand-400 transition">{{ $course->category->name }}</a>
            @endif
        </nav>

        <div class="lg:pr-[22rem]">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">{{ $course->title }}</h1>

            <div class="flex flex-wrap items-center gap-x-5 gap-y-3 mt-5 text-sm text-neutral-600 dark:text-neutral-300">
                <div class="flex items-center gap-1">
                    <span class="font-bold text-amber-500">{{ number_format($averageRating, 1) }}</span>
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-neutral-300 dark:text-neutral-600' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                    @endfor
                    <a href="#reviews" class="text-neutral-500 hover:text-brand-700 dark:text-neutral-400 dark:hover:text-brand-400">({{ $totalReviews }} đánh giá)</a>
                </div>
                <span>{{ number_format($course->enrollments_count) }} học viên</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold {{ $levelClasses[$course->level] ?? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200' }}">
                    {{ $levelLabels[$course->level] ?? $course->level }}
                </span>
            </div>

            @if($course->instructor)
                <p class="mt-4 text-sm text-neutral-600 dark:text-neutral-300">
                    Giảng viên:
                    <a href="{{ route('instructors.show', $course->instructor->id) }}" class="font-semibold text-brand-700 underline-offset-4 hover:text-brand-800 hover:underline dark:text-brand-400 dark:hover:text-brand-300">{{ $course->instructor->name }}</a>
                </p>
            @endif
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col-reverse lg:flex-row gap-8">

        {{-- ===== NỘI DUNG CHÍNH ===== --}}
        <div class="flex-1 min-w-0 space-y-10">

            {{-- Thư viện ảnh --}}
            @if($galleryUrls->isNotEmpty())
                <div x-data="{ active: @js($galleryUrls->first()) }">
                    <div class="rounded-xl overflow-hidden aspect-video bg-gray-100 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-800">
                        <img :src="active" src="{{ $galleryUrls->first() }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                    @if($galleryUrls->count() > 1)
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mt-3">
                            @foreach($galleryUrls as $url)
                                <button type="button" @click="active = @js($url)"
                                        class="rounded-lg overflow-hidden aspect-video bg-gray-100 dark:bg-neutral-800 ring-2 transition"
                                        :class="active === @js($url) ? 'ring-brand-700' : 'ring-transparent hover:ring-gray-300'">
                                    <img src="{{ $url }}" alt="Ảnh {{ $loop->iteration }} của khóa học" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Mô tả (Rich Text) --}}
            <section>
                <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 mb-4">Mô tả khóa học</h2>
                @if(filled($course->description))
                    <div class="rich-content">
                        {!! $course->description !!}
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-neutral-500">Giảng viên chưa cập nhật mô tả.</p>
                @endif
            </section>

            {{-- Đề cương: Danh sách chương / bài học --}}
            <section>
                <div class="flex items-end justify-between gap-4 mb-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100">Nội dung khóa học</h2>
                    <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $course->chapters->count() }} chương &bull; {{ $totalLessons }} bài học</p>
                </div>

                @if($course->chapters->isNotEmpty())
                    <div class="space-y-3" x-data="{ openChapters: [0] }">
                        @foreach($course->chapters as $chapterIndex => $chapter)
                            <div class="border border-gray-200 dark:border-neutral-800 rounded-xl overflow-hidden">
                                <button type="button"
                                    @click="openChapters.includes({{ $chapterIndex }}) ? openChapters = openChapters.filter(i => i !== {{ $chapterIndex }}) : openChapters.push({{ $chapterIndex }})"
                                    :aria-expanded="openChapters.includes({{ $chapterIndex }})"
                                    class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 dark:bg-neutral-800/40 hover:bg-gray-100 dark:hover:bg-neutral-800 transition text-left">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <span class="w-7 h-7 bg-brand-100 dark:bg-brand-500/15 text-brand-800 dark:text-brand-300 rounded-lg flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ $chapterIndex + 1 }}
                                        </span>
                                        <span class="font-semibold text-gray-800 dark:text-neutral-100 truncate">{{ $chapter->title }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3 shrink-0">
                                        <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $chapter->lessons->count() }} bài</span>
                                        <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400 transition-transform"
                                             :class="openChapters.includes({{ $chapterIndex }}) ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>

                                <div x-show="openChapters.includes({{ $chapterIndex }})" x-cloak>
                                    @forelse($chapter->lessons as $lesson)
                                        <div class="flex items-center space-x-3 px-5 py-3 border-t border-gray-100 dark:border-neutral-800 text-sm">
                                            <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                @if($lesson->video_url)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                @endif
                                            </svg>
                                            <span class="flex-1 text-gray-700 dark:text-neutral-200">{{ $lesson->title }}</span>
                                            <span class="text-xs text-gray-400 dark:text-neutral-500">{{ $lesson->video_url ? 'Video' : 'Bài đọc' }}</span>
                                        </div>
                                    @empty
                                        <div class="px-5 py-3 text-sm text-gray-400 dark:text-neutral-500 border-t border-gray-100 dark:border-neutral-800">Chưa có bài học</div>
                                    @endforelse

                                    @foreach($chapter->quizzes as $quiz)
                                        <div class="flex items-center space-x-3 px-5 py-3 border-t border-gray-100 dark:border-neutral-800 text-sm bg-amber-50 dark:bg-amber-500/10">
                                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="flex-1 text-amber-800 dark:text-amber-300 font-medium">Quiz: {{ $quiz->title }}</span>
                                            <span class="text-xs text-amber-600 dark:text-amber-400">Bài kiểm tra</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-neutral-500">Chưa có nội dung</p>
                @endif
            </section>

            {{-- Đánh giá --}}
            <section id="reviews" class="scroll-mt-24">
                <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 mb-6">Đánh giá từ học viên</h2>

                <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8 p-5 bg-gray-50 dark:bg-neutral-800/40 rounded-xl border border-gray-200 dark:border-neutral-800">
                    <div class="text-center sm:w-36 shrink-0">
                        <p class="text-5xl font-bold text-gray-900 dark:text-neutral-100">{{ number_format($averageRating, 1) }}</p>
                        <div class="flex items-center justify-center mt-1 space-x-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-gray-200 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">{{ $totalReviews }} đánh giá</p>
                    </div>

                    <div class="flex-1 space-y-1.5">
                        @foreach($ratingBreakdown as $star => $count)
                            @php($percent = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-10 shrink-0 text-gray-600 dark:text-neutral-300">{{ $star }} sao</span>
                                <div class="flex-1 h-2 rounded-full bg-gray-200 dark:bg-neutral-700 overflow-hidden">
                                    <div class="h-full rounded-full bg-amber-400" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="w-10 shrink-0 text-right text-xs text-gray-500 dark:text-neutral-400">{{ $percent }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @forelse($reviews as $review)
                    <div class="flex space-x-4 py-5 border-b border-gray-100 dark:border-neutral-800 last:border-0">
                        <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-500/15 text-brand-800 dark:text-brand-300 font-semibold flex items-center justify-center shrink-0 overflow-hidden">
                            @if($review->user && $review->user->avatar)
                                <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="{{ $review->user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ mb_strtoupper(mb_substr($review->user->name ?? 'U', 0, 1)) }}
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3 mb-1">
                                <span class="font-semibold text-gray-800 dark:text-neutral-100 text-sm truncate">{{ $review->user->name ?? 'Người dùng' }}</span>
                                <span class="text-xs text-gray-400 dark:text-neutral-500 shrink-0">{{ $review->created_at->locale('vi')->diffForHumans() }}</span>
                            </div>

                            <div class="flex items-center space-x-0.5 mb-2" aria-label="{{ $review->rating }} trên 5 sao">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                                @endfor
                            </div>

                            <p class="text-sm text-gray-700 dark:text-neutral-200 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 dark:text-neutral-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200 dark:text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <p class="text-sm">Chưa có đánh giá nào cho khóa học này</p>
                    </div>
                @endforelse

                @if($reviews->hasPages())
                    <div class="mt-6">
                        {{ $reviews->fragment('reviews')->links() }}
                    </div>
                @endif
            </section>
        </div>

        {{-- ===== SIDEBAR PHẢI: Giá + Ghi danh ===== --}}
        <aside class="w-full lg:w-80 shrink-0 lg:-mt-52">
            <div class="lg:sticky lg:top-24 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-xl overflow-hidden shadow-lg shadow-gray-200/60 dark:shadow-black/20">
                <div class="aspect-video overflow-hidden bg-gray-100 dark:bg-neutral-800">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-brand-50 text-brand-200 dark:bg-neutral-800 dark:text-neutral-600">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="mb-5">
                        @if($course->price == 0)
                            <p class="text-3xl font-bold text-brand-700 dark:text-brand-400">Miễn phí</p>
                        @else
                            <p class="text-3xl font-bold text-gray-900 dark:text-neutral-100">{{ number_format($course->price, 0, ',', '.') }} ₫</p>
                        @endif
                    </div>

                    {{-- Route ghi danh / phòng học do TV3 khai báo; khi chưa có thì hiện trạng thái chờ thay vì gửi form lỗi --}}
                    @auth
                        @if($isEnrolled)
                            @if(Route::has('learning.player'))
                                <a href="{{ route('learning.player', $course->slug) }}"
                                   class="block w-full text-center bg-brand-700 text-white font-semibold px-5 py-3 rounded-lg hover:bg-brand-800 transition mb-3">
                                    Vào học ngay
                                </a>
                            @endif
                            <p class="text-xs text-center text-brand-700 dark:text-brand-400 font-medium">✓ Bạn đã ghi danh khóa học này</p>
                        @elseif(Route::has('courses.enroll'))
                            <form method="POST" action="{{ route('courses.enroll', $course->id) }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-center bg-brand-700 text-white font-semibold px-5 py-3 rounded-lg hover:bg-brand-800 transition">
                                    Ghi danh ngay
                                </button>
                            </form>
                        @else
                            <button type="button" disabled
                                    class="block w-full text-center bg-gray-200 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400 font-semibold px-5 py-3 rounded-lg cursor-not-allowed">
                                Ghi danh sắp mở
                            </button>
                            <p class="mt-2 text-xs text-center text-gray-500 dark:text-neutral-400">Chức năng ghi danh đang được hoàn thiện.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-brand-700 text-white font-semibold px-5 py-3 rounded-lg hover:bg-brand-800 transition mb-2">
                            Đăng nhập để ghi danh
                        </a>
                        <p class="text-xs text-center text-gray-500 dark:text-neutral-400">
                            Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="font-semibold text-brand-700 dark:text-brand-400 hover:underline">Đăng ký miễn phí</a>
                        </p>
                    @endauth

                    <dl class="mt-6 text-sm divide-y divide-gray-100 dark:divide-neutral-800">
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-gray-500 dark:text-neutral-400">Trình độ</dt>
                            <dd class="font-medium text-gray-800 dark:text-neutral-100">{{ $levelLabels[$course->level] ?? $course->level }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-gray-500 dark:text-neutral-400">Danh mục</dt>
                            <dd class="font-medium text-gray-800 dark:text-neutral-100">{{ $course->category->name ?? 'Chưa phân loại' }}</dd>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-gray-500 dark:text-neutral-400">Nội dung</dt>
                            <dd class="font-medium text-gray-800 dark:text-neutral-100">{{ $course->chapters->count() }} chương · {{ $totalLessons }} bài</dd>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <dt class="text-gray-500 dark:text-neutral-400">Học viên</dt>
                            <dd class="font-medium text-gray-800 dark:text-neutral-100">{{ number_format($course->enrollments_count) }}</dd>
                        </div>
                    </dl>

                    @if($course->instructor)
                        <div class="mt-5 pt-5 border-t border-gray-100 dark:border-neutral-800">
                            <h4 class="font-semibold text-gray-800 dark:text-neutral-100 mb-3">Giảng viên</h4>
                            <a href="{{ route('instructors.show', $course->instructor->id) }}" class="flex items-center space-x-3 group">
                                <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-500/15 text-brand-800 dark:text-brand-300 font-bold flex items-center justify-center overflow-hidden shrink-0">
                                    @if($course->instructor->avatar)
                                        <img src="{{ asset('storage/' . $course->instructor->avatar) }}" alt="{{ $course->instructor->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_strtoupper(mb_substr($course->instructor->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-neutral-100 text-sm group-hover:text-brand-700 dark:group-hover:text-brand-300 transition">{{ $course->instructor->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500">Xem hồ sơ giảng viên</p>
                                </div>
                            </a>
                            @if($course->instructor->bio)
                                <p class="text-xs text-gray-600 dark:text-neutral-300 mt-3 leading-relaxed line-clamp-3">{{ $course->instructor->bio }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </aside>

    </div>
</div>
@endsection
