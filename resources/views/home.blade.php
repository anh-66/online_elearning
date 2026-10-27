@extends('layouts.public')

@section('title', 'Trang chủ')

@section('content')

{{-- ===== BANNER SLIDER ===== --}}
@if($banners->isNotEmpty())
<section class="relative overflow-hidden bg-gray-100" x-data="{ current: 0, total: {{ $banners->count() }} }" x-init="setInterval(() => { current = (current + 1) % total }, 5000)">
    <div class="relative h-72 md:h-96">
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
                <div class="w-full h-full bg-slate-800 flex items-center justify-center relative overflow-hidden">
                    <div class="text-center text-white px-6 max-w-3xl z-10">
                        <span class="inline-block px-3 py-1 bg-indigo-600 text-xs font-semibold rounded-full mb-3 uppercase tracking-wider">Thông báo & Khóa học mới</span>
                        <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold mb-4 leading-tight">{{ $banner->title }}</h1>
                        @if($banner->link_url)
                            <a href="{{ url($banner->link_url) }}" class="inline-block bg-indigo-600 text-white font-medium px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition">
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
        <button @click="current = {{ $index }}"
                class="w-2.5 h-2.5 rounded-full transition"
                :class="current === {{ $index }} ? 'bg-white' : 'bg-white/40'">
        </button>
        @endforeach
    </div>
    @endif
</section>
@else
{{-- Banner mặc định nếu chưa có banner --}}
<section class="bg-indigo-700 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Học trực tuyến cùng EduLearn</h1>
        <p class="text-xl text-indigo-200 mb-8">Hàng trăm khóa học chất lượng từ các giảng viên hàng đầu</p>
        <a href="{{ url('/courses') }}" class="inline-block bg-white text-indigo-700 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition text-lg">
            Khám phá khóa học
        </a>
    </div>
</section>
@endif

{{-- ===== 4 THẺ SỐ LIỆU ===== --}}
<section class="py-10 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center p-6 rounded-xl border border-gray-100 shadow-sm bg-gray-50">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_courses']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Khóa học</p>
            </div>

            <div class="text-center p-6 rounded-xl border border-gray-100 shadow-sm bg-gray-50">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Học viên</p>
            </div>

            <div class="text-center p-6 rounded-xl border border-gray-100 shadow-sm bg-gray-50">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_instructors']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Giảng viên</p>
            </div>

            <div class="text-center p-6 rounded-xl border border-gray-100 shadow-sm bg-gray-50">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_enrollments']) }}</p>
                <p class="text-sm text-gray-500 mt-1">Lượt ghi danh</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== Ô TRUY CẬP NHANH ===== --}}
<section class="py-10 bg-gray-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Truy cập nhanh</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ url('/courses') }}"
               class="flex items-center space-x-4 bg-white border border-gray-200 rounded-xl p-5 hover:border-indigo-400 hover:shadow-md transition">
                <div class="w-11 h-11 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Danh sách khóa học</p>
                    <p class="text-sm text-gray-500">Tìm và đăng ký học</p>
                </div>
            </a>

            @auth
            <a href="{{ url('/my-courses') }}"
               class="flex items-center space-x-4 bg-white border border-gray-200 rounded-xl p-5 hover:border-emerald-400 hover:shadow-md transition">
                <div class="w-11 h-11 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Khóa học của tôi</p>
                    <p class="text-sm text-gray-500">Tiếp tục học tập</p>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center space-x-4 bg-white border border-gray-200 rounded-xl p-5 hover:border-amber-400 hover:shadow-md transition">
                <div class="w-11 h-11 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Hồ sơ cá nhân</p>
                    <p class="text-sm text-gray-500">Cập nhật thông tin</p>
                </div>
            </a>
            @else
            <a href="{{ route('login') }}"
               class="flex items-center space-x-4 bg-white border border-gray-200 rounded-xl p-5 hover:border-emerald-400 hover:shadow-md transition">
                <div class="w-11 h-11 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Đăng nhập</p>
                    <p class="text-sm text-gray-500">Truy cập tài khoản của bạn</p>
                </div>
            </a>

            <a href="{{ route('register') }}"
               class="flex items-center space-x-4 bg-white border border-gray-200 rounded-xl p-5 hover:border-rose-400 hover:shadow-md transition">
                <div class="w-11 h-11 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Đăng ký</p>
                    <p class="text-sm text-gray-500">Tạo tài khoản mới</p>
                </div>
            </a>
            @endauth
        </div>
    </div>
</section>

{{-- ===== DANH MỤC NỔI BẬT ===== --}}
@if($featuredCategories->isNotEmpty())
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Danh mục nổi bật</h2>
            <a href="{{ url('/courses') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                Xem tất cả →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($featuredCategories as $category)
            <a href="{{ url('/courses?category=' . $category->id) }}"
               class="group flex flex-col items-center text-center p-5 bg-gray-50 border border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 transition">
                @if($category->icon)
                    <div class="w-12 h-12 flex items-center justify-center mb-3 text-2xl">{{ $category->icon }}</div>
                @else
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                @endif
                <p class="font-semibold text-gray-800 group-hover:text-indigo-700 transition text-sm">{{ $category->name }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $category->courses_count }} khóa học</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== KHÓA HỌC NỔI BẬT ===== --}}
@if($featuredCourses->isNotEmpty())
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Khóa học nổi bật</h2>
            <a href="{{ url('/courses') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                Xem tất cả →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($featuredCourses as $course)
            @include('partials.course-card', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
