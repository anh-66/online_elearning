@extends('layouts.public')

@section('title', $course->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ===== NỘI DUNG CHÍNH ===== --}}
        <div class="flex-1 min-w-0">

            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
                <a href="{{ url('/') }}" class="hover:text-indigo-600 transition">Trang chủ</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ url('/courses') }}" class="hover:text-indigo-600 transition">Khóa học</a>
                @if($course->category)
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ url('/courses?category=' . $course->category_id) }}" class="hover:text-indigo-600 transition">{{ $course->category->name }}</a>
                @endif
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-800 font-medium line-clamp-1">{{ $course->title }}</span>
            </nav>

            {{-- Tiêu đề --}}
            <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $course->title }}</h1>

            {{-- Meta: rating, students, level --}}
            <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-600">
                {{-- Rating --}}
                <div class="flex items-center space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                    <span class="font-semibold text-gray-800">{{ number_format($averageRating, 1) }}</span>
                    <span class="text-gray-400">({{ $totalReviews }} đánh giá)</span>
                </div>

                <span class="text-gray-300">|</span>

                {{-- Số học viên --}}
                <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>{{ number_format($course->enrollments_count ?? $course->enrollments()->count()) }} học viên</span>
                </span>

                <span class="text-gray-300">|</span>

                {{-- Trình độ --}}
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium
                    @if($course->level === 'beginner') bg-emerald-100 text-emerald-700
                    @elseif($course->level === 'intermediate') bg-amber-100 text-amber-700
                    @else bg-rose-100 text-rose-700 @endif">
                    @if($course->level === 'beginner') Cơ bản
                    @elseif($course->level === 'intermediate') Trung cấp
                    @else Nâng cao @endif
                </span>
            </div>

            {{-- Ảnh thumbnail / Gallery --}}
            @if($course->images->isNotEmpty())
            <div class="mb-8">
                <div class="rounded-xl overflow-hidden aspect-video bg-gray-100">
                    <img src="{{ asset('storage/' . $course->images->first()->path) }}"
                         alt="{{ $course->title }}"
                         class="w-full h-full object-cover">
                </div>
                @if($course->images->count() > 1)
                <div class="grid grid-cols-4 gap-2 mt-2">
                    @foreach($course->images->skip(1)->take(4) as $image)
                    <div class="rounded-lg overflow-hidden aspect-video bg-gray-100">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @elseif($course->thumbnail)
            <div class="mb-8 rounded-xl overflow-hidden aspect-video bg-gray-100">
                <img src="{{ asset('storage/' . $course->thumbnail) }}"
                     alt="{{ $course->title }}"
                     class="w-full h-full object-cover">
            </div>
            @endif

            {{-- Mô tả (Rich Text) --}}
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Mô tả khóa học</h2>
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                    {!! $course->description !!}
                </div>
            </div>

            {{-- Đề cương: Danh sách chương / bài học --}}
            <div class="mb-10">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Nội dung khóa học</h2>

                @php
                    $totalLessons = $course->chapters->sum(fn($ch) => $ch->lessons->count());
                @endphp
                <p class="text-sm text-gray-500 mb-4">
                    {{ $course->chapters->count() }} chương &bull; {{ $totalLessons }} bài học
                </p>

                @if($course->chapters->isNotEmpty())
                <div class="space-y-3" x-data="{ openChapters: [0] }">
                    @foreach($course->chapters as $chapterIndex => $chapter)
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        {{-- Chapter header --}}
                        <button
                            @click="openChapters.includes({{ $chapterIndex }}) ? openChapters = openChapters.filter(i => i !== {{ $chapterIndex }}) : openChapters.push({{ $chapterIndex }})"
                            class="w-full flex items-center justify-between px-5 py-4 bg-gray-50 hover:bg-gray-100 transition text-left">
                            <div class="flex items-center space-x-3">
                                <span class="w-7 h-7 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ $chapterIndex + 1 }}
                                </span>
                                <span class="font-semibold text-gray-800">{{ $chapter->title }}</span>
                            </div>
                            <div class="flex items-center space-x-3 shrink-0">
                                <span class="text-xs text-gray-400">{{ $chapter->lessons->count() }} bài</span>
                                <svg class="w-4 h-4 text-gray-500 transition-transform"
                                     :class="openChapters.includes({{ $chapterIndex }}) ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>

                        {{-- Lessons list --}}
                        <div x-show="openChapters.includes({{ $chapterIndex }})" x-cloak>
                            @forelse($chapter->lessons as $lessonIndex => $lesson)
                            <div class="flex items-center space-x-3 px-5 py-3 border-t border-gray-100 text-sm">
                                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($lesson->video_url)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    @endif
                                </svg>
                                <span class="flex-1 text-gray-700">{{ $lesson->title }}</span>
                                @if($lesson->video_url && !$isEnrolled && $lessonIndex === 0)
                                <span class="text-xs text-indigo-600 font-medium">Xem thử</span>
                                @endif
                            </div>
                            @empty
                            <div class="px-5 py-3 text-sm text-gray-400 border-t border-gray-100">Chưa có bài học</div>
                            @endforelse

                            {{-- Quiz của chương --}}
                            @foreach($chapter->quizzes as $quiz)
                            <div class="flex items-center space-x-3 px-5 py-3 border-t border-gray-100 text-sm bg-amber-50">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="flex-1 text-amber-700 font-medium">Quiz: {{ $quiz->title }}</span>
                                <span class="text-xs text-amber-500">Bài kiểm tra</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">Chưa có nội dung</p>
                @endif
            </div>

            {{-- Đánh giá & Bình luận --}}
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6">Đánh giá từ học viên</h2>

                {{-- Tổng quan rating --}}
                <div class="flex items-center space-x-6 mb-8 p-5 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="text-center">
                        <p class="text-5xl font-bold text-gray-900">{{ number_format($averageRating, 1) }}</p>
                        <div class="flex items-center justify-center mt-1 space-x-0.5">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($averageRating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $totalReviews }} đánh giá</p>
                    </div>
                </div>

                {{-- Danh sách đánh giá --}}
                @forelse($reviews as $review)
                <div class="flex space-x-4 py-5 border-b border-gray-100 last:border-0">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center shrink-0 overflow-hidden">
                        @if($review->user && $review->user->avatar)
                            <img src="{{ asset('storage/' . $review->user->avatar) }}" alt="{{ $review->user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-gray-800 text-sm">{{ $review->user->name ?? 'Người dùng' }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>

                        {{-- Stars --}}
                        <div class="flex items-center space-x-0.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                        </div>

                        <p class="text-sm text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <p class="text-sm">Chưa có đánh giá nào cho khóa học này</p>
                </div>
                @endforelse

                {{-- Phân trang đánh giá --}}
                @if($reviews->hasPages())
                <div class="mt-6">
                    {{ $reviews->appends(request()->except('review_page'))->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- ===== SIDEBAR PHẢI: Thông tin + Đăng ký ===== --}}
        <aside class="w-full lg:w-80 shrink-0">
            <div class="sticky top-20 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">

                {{-- Thumbnail preview --}}
                @if($course->thumbnail)
                <div class="aspect-video overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                         alt="{{ $course->title }}"
                         class="w-full h-full object-cover">
                </div>
                @endif

                <div class="p-5">
                    {{-- Giá --}}
                    <div class="mb-5">
                        @if($course->price == 0)
                            <p class="text-3xl font-bold text-emerald-600">Miễn phí</p>
                        @else
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($course->price, 0, ',', '.') }} ₫</p>
                        @endif
                    </div>

                    {{-- Nút đăng ký / học tiếp --}}
                    @auth
                        @if($isEnrolled)
                            <a href="{{ url('/my-courses') }}"
                               class="block w-full text-center bg-emerald-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-emerald-700 transition mb-3">
                                Vào học ngay
                            </a>
                            <p class="text-xs text-center text-emerald-600 font-medium">✓ Bạn đã ghi danh khóa học này</p>
                        @else
                            <form method="POST" action="{{ url('/courses/' . $course->id . '/enroll') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-center bg-indigo-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-indigo-700 transition">
                                    Ghi danh ngay
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full text-center bg-indigo-600 text-white font-semibold px-5 py-3 rounded-lg hover:bg-indigo-700 transition mb-2">
                            Đăng nhập để ghi danh
                        </a>
                        <p class="text-xs text-center text-gray-500">
                            Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Đăng ký miễn phí</a>
                        </p>
                    @endauth

                    {{-- Thông tin khóa học --}}
                    <div class="mt-6 space-y-3 text-sm">
                        <h4 class="font-semibold text-gray-800">Thông tin khóa học</h4>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">Trình độ</span>
                            <span class="font-medium text-gray-800">
                                @if($course->level === 'beginner') Cơ bản
                                @elseif($course->level === 'intermediate') Trung cấp
                                @else Nâng cao @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">Danh mục</span>
                            <span class="font-medium text-gray-800">{{ $course->category->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">Số bài học</span>
                            <span class="font-medium text-gray-800">
                                {{ $course->chapters->sum(fn($ch) => $ch->lessons->count()) }} bài
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500">Học viên</span>
                            <span class="font-medium text-gray-800">{{ number_format($course->enrollments()->count()) }}</span>
                        </div>
                    </div>

                    {{-- Giảng viên --}}
                    @if($course->instructor)
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <h4 class="font-semibold text-gray-800 mb-3">Giảng viên</h4>
                        <a href="{{ route('instructors.show', $course->instructor->id) }}"
                           class="flex items-center space-x-3 group">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center overflow-hidden shrink-0">
                                @if($course->instructor->avatar)
                                    <img src="{{ asset('storage/' . $course->instructor->avatar) }}"
                                         alt="{{ $course->instructor->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm group-hover:text-indigo-700 transition">
                                    {{ $course->instructor->name }}
                                </p>
                                <p class="text-xs text-gray-400">Giảng viên</p>
                            </div>
                        </a>
                        @if($course->instructor->bio)
                        <p class="text-xs text-gray-600 mt-3 leading-relaxed line-clamp-3">
                            {{ $course->instructor->bio }}
                        </p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </aside>

    </div>
</div>
@endsection
