@extends('layouts.public')

@section('title', $instructor->name . ' - Giảng viên')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ===== PROFILE GIẢNG VIÊN ===== --}}
    <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-3xl p-6 sm:p-8 shadow-sm mb-10 transition-colors">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">

            {{-- Avatar --}}
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gray-50 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 p-1 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                @if($instructor->avatar_url)
                    <img src="{{ $instructor->avatar_url }}" referrerpolicy="no-referrer"
                         alt="{{ $instructor->name }}"
                         class="w-full h-full object-cover rounded-xl"
                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                    <span class="hidden flex items-center justify-center w-full h-full font-bold text-2xl text-[#019D91] dark:text-[#02b3a5]">{{ $instructor->initialLetter() }}</span>
                @else
                    <span class="font-bold text-2xl text-[#019D91] dark:text-[#02b3a5]">{{ $instructor->initialLetter() }}</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold uppercase tracking-wider text-[#019D91] dark:text-[#02b3a5] mb-1">GIẢNG VIÊN</p>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $instructor->name }}</h1>
                    <span class="inline-flex px-2.5 py-0.5 text-xs font-semibold bg-teal-50 dark:bg-teal-900/30 text-[#019D91] dark:text-[#02b3a5] rounded-full border border-teal-100 dark:border-teal-800/40">
                        Giảng viên
                    </span>
                </div>

                @if($instructor->bio)
                    <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base leading-relaxed mt-2.5 max-w-3xl">{{ $instructor->bio }}</p>
                @else
                    <p class="text-gray-400 text-sm mt-2.5">Giảng viên chưa cập nhật tiểu sử.</p>
                @endif

                {{-- Stats --}}
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 pt-5 mt-5 border-t border-gray-100 dark:border-neutral-800">
                    <div class="px-5 py-3 rounded-2xl bg-gray-50/70 dark:bg-neutral-800/60 border border-gray-100 dark:border-neutral-800 min-w-[120px]">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">{{ $courses->total() }}</p>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mt-1.5">Khóa học</p>
                    </div>
                    <div class="px-5 py-3 rounded-2xl bg-gray-50/70 dark:bg-neutral-800/60 border border-gray-100 dark:border-neutral-800 min-w-[120px]">
                        <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">{{ number_format($totalStudents) }}</p>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mt-1.5">Học viên</p>
                    </div>
                    <div class="px-5 py-3 rounded-2xl bg-gray-50/70 dark:bg-neutral-800/60 border border-gray-100 dark:border-neutral-800 min-w-[120px]">
                        <p class="text-2xl font-bold text-[#019D91] dark:text-[#02b3a5] leading-none">{{ number_format($averageRating ?? 0, 1) }} ★</p>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mt-1.5">Đánh giá TB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DANH SÁCH KHÓA HỌC ===== --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 mb-6">
            Khóa học của {{ $instructor->name }}
            <span class="text-base font-normal text-gray-400 dark:text-neutral-500">({{ $courses->total() }} khóa)</span>
        </h2>

        @if($courses->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($courses as $course)
            @include('partials.course-card', ['course' => $course])
            @endforeach
        </div>

        {{-- Phân trang --}}
        @if($courses->hasPages())
        <div class="mt-10">
            {{ $courses->links() }}
        </div>
        @endif
        @else
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-neutral-800 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-neutral-400 text-sm">Giảng viên này chưa có khóa học nào được công bố.</p>
        </div>
        @endif
    </div>

</div>
@endsection
