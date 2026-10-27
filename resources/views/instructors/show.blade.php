@extends('layouts.public')

@section('title', $instructor->name . ' - Giảng viên')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- ===== PROFILE GIẢNG VIÊN ===== --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 md:p-8 mb-10 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">

            {{-- Avatar --}}
            <div class="w-24 h-24 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-3xl overflow-hidden shrink-0">
                @if($instructor->avatar)
                    <img src="{{ asset('storage/' . $instructor->avatar) }}"
                         alt="{{ $instructor->name }}"
                         class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($instructor->name, 0, 1)) }}
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $instructor->name }}</h1>
                    <span class="inline-flex px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 rounded-full">
                        Giảng viên
                    </span>
                </div>

                @if($instructor->bio)
                <p class="text-gray-600 text-sm leading-relaxed mb-4 max-w-2xl">{{ $instructor->bio }}</p>
                @else
                <p class="text-gray-400 text-sm mb-4">Giảng viên chưa cập nhật tiểu sử.</p>
                @endif

                {{-- Stats --}}
                <div class="flex flex-wrap gap-6">
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-900">{{ $courses->total() }}</p>
                        <p class="text-xs text-gray-500">Khóa học</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-900">{{ number_format($totalStudents) }}</p>
                        <p class="text-xs text-gray-500">Học viên</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-900">{{ number_format($averageRating ?? 0, 1) }}</p>
                        <p class="text-xs text-gray-500">Đánh giá TB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== DANH SÁCH KHÓA HỌC ===== --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-6">
            Khóa học của {{ $instructor->name }}
            <span class="text-base font-normal text-gray-400">({{ $courses->total() }} khóa)</span>
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
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm">Giảng viên này chưa có khóa học nào được công bố.</p>
        </div>
        @endif
    </div>

</div>
@endsection
