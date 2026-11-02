@extends('layouts.public')

@section('title', 'Trang chủ')

@php
    // Tên icon lưu ở cột categories.icon (VD seeder: 'code', 'chart-bar', 'palette') → path SVG Heroicons
    $categoryIcons = [
        'code' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
        'chart-bar' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'palette' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
        'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'translate' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
        'default' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    ];

    $heroStats = [
        ['value' => $stats['total_courses'], 'label' => 'Khóa học', 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
        ['value' => $stats['total_students'], 'label' => 'Học viên', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['value' => $stats['total_instructors'], 'label' => 'Giảng viên', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['value' => $stats['total_enrollments'], 'label' => 'Lượt ghi danh', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
    ];

    $quickLinks = [
        ['label' => 'Khóa học', 'text' => 'Khám phá thư viện', 'url' => route('courses.index'), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Miễn phí', 'text' => 'Học không tốn phí', 'url' => route('courses.index', ['price' => 'free']), 'icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7'],
    ];
    if (Auth::check()) {
        $quickLinks[] = Route::has('my-courses')
            ? ['label' => 'Góc học tập', 'text' => 'Tiến độ của bạn', 'url' => route('my-courses'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4']
            : ['label' => 'Bảng điều khiển', 'text' => 'Tiếp tục học tập', 'url' => route(Auth::user()->dashboardRouteName()), 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'];
        $quickLinks[] = ['label' => 'Hồ sơ', 'text' => 'Cập nhật thông tin', 'url' => route('profile.edit'), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'];
    } else {
        $quickLinks[] = ['label' => 'Đăng nhập', 'text' => 'Vào tài khoản', 'url' => route('login'), 'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'];
        $quickLinks[] = ['label' => 'Đăng ký', 'text' => 'Tạo tài khoản mới', 'url' => route('register'), 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'];
    }
@endphp

@section('content')

{{-- ===== HERO ===== --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-brand-500 text-white dark:from-brand-950 dark:via-brand-900 dark:to-neutral-950">
    <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>
    <div class="absolute -bottom-40 left-1/3 h-96 w-96 rounded-full bg-brand-300/20 blur-3xl" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20 sm:pt-20 sm:pb-24">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-white/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Nền tảng học trực tuyến cho sinh viên
            </span>
            <h1 class="mt-5 text-4xl sm:text-5xl font-bold leading-tight tracking-tight">
                Nâng tầm kiến thức<br>cùng <span class="text-amber-300">EduLearn</span>
            </h1>
            <p class="mt-5 text-base sm:text-lg leading-relaxed text-brand-50/90">
                Học lập trình, dữ liệu, thiết kế và kỹ năng mềm với lộ trình rõ ràng, bài giảng video và theo dõi tiến độ từng bài.
            </p>

            <form action="{{ route('courses.index') }}" method="GET" class="mt-8 flex items-center gap-2 rounded-xl bg-white p-2 shadow-xl shadow-brand-900/20 dark:bg-neutral-900">
                <input type="text" name="keyword" placeholder="Bạn muốn học gì hôm nay?"
                       class="flex-1 min-w-0 border-0 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-0 dark:!bg-transparent dark:text-neutral-100">
                <button type="submit" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tìm khóa học
                </button>
            </form>
        </div>

        <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($heroStats as $stat)
                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm">
                    <svg class="w-7 h-7 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $stat['icon'] }}"/></svg>
                    <p class="mt-3 text-3xl font-bold">{{ number_format($stat['value']) }}+</p>
                    <p class="mt-0.5 text-sm text-brand-50/90">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== Ô TRUY CẬP NHANH (đè lên mép hero) ===== --}}
<section class="relative -mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($quickLinks as $link)
            <a href="{{ $link['url'] }}"
               class="group flex flex-col items-center rounded-xl border-b-2 border-transparent bg-white p-5 text-center shadow-md shadow-slate-200/70 transition hover:-translate-y-1 hover:border-brand-600 dark:bg-neutral-900 dark:shadow-black/30 dark:hover:border-brand-400">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-700 transition group-hover:bg-brand-700 group-hover:text-white dark:bg-brand-500/15 dark:text-brand-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $link['icon'] }}"/></svg>
                </span>
                <span class="mt-3 font-semibold text-gray-900 dark:text-neutral-100">{{ $link['label'] }}</span>
                <span class="mt-0.5 text-xs text-gray-500 dark:text-neutral-400">{{ $link['text'] }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- ===== BANNER (Admin quản lý) ===== --}}
@if($banners->isNotEmpty())
<section class="mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="relative overflow-hidden rounded-2xl bg-brand-50 dark:bg-neutral-900" x-data="{ current: 0, total: {{ $banners->count() }} }" x-init="setInterval(() => { current = (current + 1) % total }, 5000)">
        <div class="relative h-56 md:h-80">
            @foreach($banners as $index => $banner)
            <div class="absolute inset-0 transition-opacity duration-700"
                 x-show="current === {{ $index }}"
                 x-cloak>
                @php
                    $hasImage = $banner->image_path && file_exists(public_path('storage/' . $banner->image_path));
                @endphp
                @if($hasImage)
                    <img src="{{ asset('storage/' . $banner->image_path) }}"
                         alt="{{ $banner->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-r from-brand-800 to-brand-600 flex items-center px-8 sm:px-12 relative overflow-hidden dark:from-brand-950 dark:to-brand-800">
                        <div class="absolute -right-16 -top-16 h-72 w-72 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
                        <div class="relative max-w-2xl text-white">
                            <span class="inline-block px-3 py-1 bg-amber-300 text-brand-950 text-xs font-semibold rounded-full mb-3 uppercase tracking-wider">Thông báo & Khóa học mới</span>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 leading-tight">{{ $banner->title }}</h2>
                            @if($banner->link_url)
                                <a href="{{ url($banner->link_url) }}" class="inline-block bg-white text-brand-800 font-semibold px-5 py-2.5 rounded-lg hover:bg-brand-50 transition">
                                    Khám phá ngay
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Dots --}}
        @if($banners->count() > 1)
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
            @foreach($banners as $index => $banner)
            <button type="button" @click="current = {{ $index }}" aria-label="Banner {{ $index + 1 }}"
                    class="w-2.5 h-2.5 rounded-full transition"
                    :class="current === {{ $index }} ? 'bg-white' : 'bg-white/40'">
            </button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endif

{{-- ===== DANH MỤC NỔI BẬT ===== --}}
@if($featuredCategories->isNotEmpty())
<section class="mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-neutral-100">Danh mục nổi bật</h2>
            <p class="mt-1 text-gray-500 dark:text-neutral-400">Chọn lĩnh vực bạn quan tâm</p>
        </div>
        <a href="{{ route('courses.index') }}" class="shrink-0 text-sm font-semibold text-brand-700 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300 transition">
            Xem tất cả →
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($featuredCategories as $category)
        @php($iconPath = $categoryIcons[$category->icon] ?? null)
        <a href="{{ route('courses.index', ['category' => $category->id]) }}"
           class="group flex items-center gap-3 p-4 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-xl hover:border-brand-600 hover:shadow-md transition dark:hover:border-brand-500">
            @if($category->icon && ! $iconPath && ! preg_match('/^[a-z0-9-]+$/', $category->icon))
                {{-- Icon dạng emoji thì hiển thị trực tiếp --}}
                <div class="w-11 h-11 shrink-0 flex items-center justify-center text-2xl">{{ $category->icon }}</div>
            @else
                <div class="w-11 h-11 shrink-0 bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 rounded-lg flex items-center justify-center group-hover:bg-brand-700 group-hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath ?? $categoryIcons['default'] }}"/>
                    </svg>
                </div>
            @endif
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 dark:text-neutral-100 group-hover:text-brand-800 dark:group-hover:text-brand-300 transition text-sm truncate">{{ $category->name }}</p>
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $category->courses_count }} khóa học</p>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ===== KHÓA HỌC NỔI BẬT ===== --}}
@if($featuredCourses->isNotEmpty())
<section class="mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-neutral-100">Khóa học được yêu thích</h2>
            <p class="mt-1 text-gray-500 dark:text-neutral-400">Nhiều học viên lựa chọn nhất</p>
        </div>
        <a href="{{ route('courses.index') }}" class="shrink-0 text-sm font-semibold text-brand-700 dark:text-brand-400 hover:text-brand-900 dark:hover:text-brand-300 transition">
            Xem tất cả →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach($featuredCourses as $course)
        @include('partials.course-card', ['course' => $course])
        @endforeach
    </div>
</section>
@endif

{{-- ===== KÊU GỌI HÀNH ĐỘNG ===== --}}
<section class="mt-14 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-700 to-brand-500 px-6 py-10 text-center text-white sm:px-12 dark:from-brand-900 dark:to-brand-700">
        <div class="absolute -left-16 -bottom-20 h-64 w-64 rounded-full bg-white/10 blur-2xl" aria-hidden="true"></div>
        <h2 class="relative text-2xl sm:text-3xl font-bold">Sẵn sàng bắt đầu hành trình học tập?</h2>
        <p class="relative mx-auto mt-3 max-w-xl text-brand-50/90">Hàng chục khóa học chất lượng đang chờ bạn. Học theo tốc độ của riêng bạn, mọi lúc, mọi nơi.</p>
        <div class="relative mt-6 flex flex-wrap justify-center gap-3">
            @guest
                <a href="{{ route('register') }}" class="rounded-lg bg-white px-6 py-3 text-sm font-semibold text-brand-800 transition hover:bg-brand-50">Đăng ký miễn phí</a>
            @endguest
            <a href="{{ route('courses.index') }}" class="rounded-lg bg-white/15 px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/30 transition hover:bg-white/25">Xem khóa học</a>
        </div>
    </div>
</section>

@endsection
