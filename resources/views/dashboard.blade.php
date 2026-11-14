@extends('layouts.public')

@section('title', 'Bảng điều khiển')

@section('content')
@php
    $user = Auth::user();
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $completedTotal = $completedTotal ?? $user->enrollments()->where('progress_percent', 100)->count();
    $averageProgress = $averageProgress ?? (int) round($user->enrollments()->avg('progress_percent') ?? 0);
    $quickLinks = [
        [
            'id' => 'quick-my-courses',
            'label' => 'Khóa học của tôi',
            'text' => 'Tiếp tục các bài học bạn đang theo dõi.',
            'url' => route('my-courses'),
            'icon' => 'book-open',
            'iconBg' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
        ],
        [
            'id' => 'quick-explore',
            'label' => 'Khám phá khóa học',
            'text' => 'Tìm khóa học mới theo chủ đề, trình độ.',
            'url' => route('courses.index'),
            'icon' => 'compass',
            'iconBg' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400',
        ],
        [
            'id' => 'quick-free',
            'label' => 'Khóa học miễn phí',
            'text' => 'Bắt đầu ngay với các tài liệu không tốn phí.',
            'url' => route('courses.index', ['price' => 'free']),
            'icon' => 'gift',
            'iconBg' => 'bg-teal-50 text-[#019D91] dark:bg-teal-950/40 dark:text-teal-400',
        ],
        [
            'id' => 'quick-profile',
            'label' => 'Hồ sơ cá nhân',
            'text' => 'Cập nhật thông tin và cài đặt tài khoản.',
            'url' => route('profile.edit'),
            'icon' => 'user',
            'iconBg' => 'bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400',
        ],
    ];
@endphp

<div class="bg-[#fcfcfc] py-10 dark:bg-neutral-950">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Hero Greeting Section -->
        <section id="greeting-section" class="rounded-3xl bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 p-6 sm:p-8 md:p-10 mb-8 sm:mb-10 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 sm:gap-8">
                <div class="flex-1 min-w-0">
                    <span class="text-[#019D91] dark:text-teal-400 font-bold text-xs tracking-widest uppercase">Bảng điều khiển</span>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 dark:text-white mt-2 tracking-tight uppercase">
                        Xin chào, {{ $user->name }}
                    </h1>
                    <p class="text-gray-500 dark:text-neutral-400 mt-2 text-sm sm:text-base leading-relaxed max-w-2xl">
                        Hôm nay bạn muốn học gì? Tiếp tục khóa học đang dở hoặc khám phá các chủ đề mới để phát triển kỹ năng của mình.
                    </p>
                </div>
                
                <div class="flex items-center gap-4 shrink-0">
                    <div class="bg-gray-50 dark:bg-[#0d1117] p-5 sm:p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 min-w-[140px] text-center">
                        <span class="block text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($enrollmentTotal) }}</span>
                        <span class="text-xs text-gray-400 dark:text-neutral-400 uppercase tracking-wide mt-1 block">Khóa học đã đăng ký</span>
                    </div>
                    <a href="{{ route('courses.index') }}" id="cta-explore" class="inline-flex items-center justify-center px-7 sm:px-8 py-3.5 sm:py-4 bg-[#019D91] hover:bg-[#008b80] text-white font-semibold rounded-xl transition-all shadow-lg shadow-[#019D91]/20 active:scale-95 text-sm sm:text-base">
                        Tìm khóa học
                    </a>
                </div>
            </div>
        </section>

        <!-- Stats Row (Hàng Thống Kê Tiến Độ Theo Thiết Kế) -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6 mb-12 sm:mb-14">
            <div class="bg-white dark:bg-[#161b22] p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-neutral-400 uppercase font-medium mb-1">Đã ghi danh</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($enrollmentTotal) }}</p>
            </div>
            <a href="{{ route('certificates.index') }}" id="stat-completed" class="group block bg-white dark:bg-[#161b22] p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm hover:border-[#019D91]/40 dark:hover:border-[#019D91]/40 hover:shadow-md transition-all">
                <p class="text-xs text-gray-400 dark:text-neutral-400 uppercase font-medium mb-1">Đã hoàn thành</p>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($completedTotal) }}</p>
                <p class="mt-2 text-xs font-bold text-[#019D91] group-hover:underline">Chứng chỉ của tôi →</p>
            </a>
            <div class="bg-white dark:bg-[#161b22] p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm">
                <p class="text-xs text-gray-400 dark:text-neutral-400 uppercase font-medium mb-1">Tiến độ trung bình</p>
                <p class="text-3xl font-bold text-[#019D91] dark:text-teal-400">{{ $averageProgress }}%</p>
            </div>
        </section>

        <!-- Quick Access Grid -->
        <section id="quick-access" class="mb-14 sm:mb-16">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6 uppercase tracking-wider">Truy cập nhanh</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                @foreach ($quickLinks as $link)
                    <a href="{{ $link['url'] }}" id="{{ $link['id'] }}" class="group p-6 bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl hover:border-[#019D91]/40 dark:hover:border-[#019D91]/40 hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 rounded-xl {{ $link['iconBg'] }} flex items-center justify-center shrink-0">
                                @if($link['icon'] === 'book-open')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                                @elseif($link['icon'] === 'compass')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg>
                                @elseif($link['icon'] === 'gift')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                @endif
                            </div>
                            <svg class="w-5 h-5 text-gray-300 dark:text-neutral-600 group-hover:text-[#019D91] transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-[#019D91] transition-colors">{{ $link['label'] }}</h3>
                        <p class="text-sm text-gray-400 dark:text-neutral-400 mt-2 leading-snug">{{ $link['text'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Recent Courses -->
        <section id="recent-courses">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white uppercase tracking-tight">KHÓA HỌC CỦA TÔI</h2>
                    <p class="text-gray-500 dark:text-neutral-400 mt-1 text-sm">Tiếp tục các khóa học bạn đã ghi danh.</p>
                </div>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center self-start sm:self-auto px-5 sm:px-6 py-2.5 bg-[#019D91] hover:bg-[#008b80] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Khám phá khóa học
                </a>
            </div>

            @if ($enrolledCourses->isEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    {{-- Empty Message Card --}}
                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-neutral-800 bg-white dark:bg-[#161b22] p-8 text-center flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-teal-50 dark:bg-teal-950/40 text-[#019D91] flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </div>
                        <p class="font-semibold text-slate-900 dark:text-white">Bạn chưa ghi danh khóa học nào</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">Hãy chọn một khóa học phù hợp để bắt đầu hành trình học tập.</p>
                    </div>

                    {{-- Add New Course Card --}}
                    <a href="{{ route('courses.index') }}" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 dark:border-neutral-800 rounded-2xl group hover:border-[#019D91] hover:bg-[#019D91]/5 transition-all cursor-pointer bg-white dark:bg-[#161b22]">
                        <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-neutral-800 text-gray-400 flex items-center justify-center mb-4 group-hover:bg-[#019D91]/10 group-hover:text-[#019D91] transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </div>
                        <p class="font-medium text-gray-700 dark:text-neutral-200 group-hover:text-[#019D91]">Đăng ký khóa học mới</p>
                        <p class="text-xs text-gray-400 mt-1">Khám phá kho tàng kiến thức</p>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach ($enrolledCourses as $course)
                        @php
                            $rating = (float) ($course->reviews_avg_rating ?? $course->reviews->avg('rating') ?? 0);
                            $reviewsCount = $course->reviews_count ?? $course->reviews->count();
                        @endphp
                        <div class="course-card bg-white dark:bg-[#161b22] rounded-3xl border border-gray-100 dark:border-neutral-800 overflow-hidden transition-all shadow-sm hover:shadow-md hover:border-[#019D91]/40 flex flex-col justify-between">
                            {{-- Thumbnail / Media --}}
                            @if($course->thumbnail)
                                <a href="{{ route('courses.show', $course->slug) }}" class="relative aspect-[16/10] overflow-hidden block bg-[#f0f2f5] dark:bg-[#0d1117]">
                                    <img src="{{ Storage::disk('public')->url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                </a>
                            @else
                                <a href="{{ route('courses.show', $course->slug) }}" class="relative aspect-[16/10] bg-[#f0f2f5] dark:bg-[#0d1117] flex items-center justify-center p-8 block">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 rounded-xl border border-gray-300 dark:border-neutral-700 flex items-center justify-center mb-2 text-gray-400 dark:text-neutral-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-neutral-400 font-medium">{{ $course->category->name ?? 'Lập trình Web' }}</span>
                                    </div>
                                </a>
                            @endif

                            {{-- Card Body --}}
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight line-clamp-1 flex-1 min-w-0" title="{{ $course->title }}">
                                            <a href="{{ route('courses.show', $course->slug) }}" class="hover:text-[#019D91] transition-colors">
                                                {{ $course->title }}
                                            </a>
                                        </h3>
                                        <div class="flex gap-1.5 shrink-0">
                                            <span class="px-2 py-0.5 border border-gray-200 dark:border-neutral-700 text-[10px] font-medium rounded-full text-gray-500 dark:text-neutral-400 bg-white dark:bg-neutral-800">
                                                {{ $levelLabels[$course->level] ?? 'Cơ bản' }}
                                            </span>
                                            @if((float) $course->price <= 0)
                                                <span class="px-2 py-0.5 bg-teal-50 dark:bg-teal-950/40 text-[10px] font-medium rounded-full text-[#019D91] dark:text-teal-400">
                                                    Miễn phí
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 bg-gray-50 dark:bg-neutral-800 text-[10px] font-medium rounded-full text-gray-600 dark:text-neutral-300">
                                                    {{ number_format($course->price, 0, ',', '.') }} ₫
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-400 dark:text-neutral-400 mb-4 truncate">
                                        {{ $course->instructor->name ?? 'EduLearn' }}
                                    </p>

                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-1 text-xs font-bold text-slate-900 dark:text-white">
                                            {{ number_format($rating, 1) }}
                                            <svg class="w-3.5 h-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span class="text-gray-400 dark:text-neutral-500 font-normal ml-0.5">({{ $reviewsCount }})</span>
                                        </div>
                                        <span class="text-[11px] text-gray-400 dark:text-neutral-400">
                                            {{ number_format($course->enrollments_count ?? 0) }} học viên
                                        </span>
                                    </div>

                                    <div class="flex gap-3 mb-4">
                                        <a href="{{ route('courses.show', $course->slug) }}" class="flex-1 py-2.5 bg-[#019D91] hover:bg-[#008b80] text-white text-xs font-bold rounded-xl transition-colors text-center shadow-sm">
                                            Xem khóa học
                                        </a>
                                        <button type="button"
                                                onclick="navigator.clipboard?.writeText('{{ route('courses.show', $course->slug) }}'); alert('Đã sao chép liên kết khóa học!');"
                                                class="px-4 py-2.5 border border-gray-200 dark:border-neutral-700 hover:bg-gray-50 dark:hover:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs font-bold rounded-xl transition-colors">
                                            Chia sẻ
                                        </button>
                                    </div>

                                    <div class="pt-3 border-t border-gray-100 dark:border-neutral-800">
                                        <p class="text-[10px] text-center text-gray-400 dark:text-neutral-500">
                                            {{ $course->category->name ?? 'Lập trình Web' }} • {{ $levelLabels[$course->level] ?? 'Cơ bản' }} • EduLearn
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress Bar (Nằm bên trong thẻ theo đúng thiết kế) --}}
                            <div class="px-6 pb-6 pt-2">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[11px] font-medium text-gray-500 dark:text-neutral-400">Tiến độ học tập</span>
                                    <span class="text-[11px] font-bold text-slate-900 dark:text-white">{{ $course->pivot->progress_percent ?? 0 }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-100 dark:bg-neutral-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#019D91] rounded-full transition-all duration-300" style="width: {{ min(100, max(0, (int) ($course->pivot->progress_percent ?? 0))) }}%"></div>
                                </div>
                                @if((int) ($course->pivot->progress_percent ?? 0) >= 100)
                                    <a href="{{ route('courses.certificate', $course->slug) }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#019D91] hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="6"></circle><path d="M8.5 14.2L7 21l5-2.6L17 21l-1.5-6.8"></path></svg>
                                        Xem chứng chỉ
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Add New Card -->
                    <a href="{{ route('courses.index') }}" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-gray-200 dark:border-neutral-800 rounded-2xl group hover:border-[#019D91] hover:bg-[#019D91]/5 transition-all cursor-pointer bg-white dark:bg-[#161b22] min-h-[260px]">
                        <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-neutral-800 text-gray-400 flex items-center justify-center mb-4 group-hover:bg-[#019D91]/10 group-hover:text-[#019D91] transition-all">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                        </div>
                        <p class="font-medium text-gray-700 dark:text-neutral-200 group-hover:text-[#019D91]">Đăng ký khóa học mới</p>
                        <p class="text-xs text-gray-400 mt-1">Khám phá kho tàng kiến thức</p>
                    </a>
                </div>
            @endif
        </section>

        {{-- Section 3: Wishlist Courses --}}
        <section class="mt-14 sm:mt-16">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white uppercase tracking-tight">DANH SÁCH YÊU THÍCH</h2>
                    <p class="text-gray-500 dark:text-neutral-400 mt-1 text-sm">Các khóa học bạn đã lưu để học sau.</p>
                </div>
            </div>

            @if ($wishlistCourses->isEmpty())
                <div class="p-8 text-center bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-3xl">
                    <p class="text-sm text-gray-500 dark:text-neutral-400">Bạn chưa thêm khóa học nào vào danh sách yêu thích.</p>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center mt-3 text-xs font-bold text-[#019D91] hover:underline">
                        Khám phá khóa học ngay &rarr;
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($wishlistCourses as $course)
                        @include('partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            @endif
        </section>

    </div>
</div>
@endsection
