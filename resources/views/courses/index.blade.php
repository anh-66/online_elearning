@extends('layouts.public')

@section('title', 'Danh sách Khóa học')

@section('content')
<div class="w-full min-h-screen bg-white dark:bg-[#0d1117]">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ===== SIDEBAR BỘ LỌC (GitHub Dark Bordered Surface) ===== --}}
        <aside class="w-full lg:w-64 shrink-0">
            <form method="GET" action="{{ url('/courses') }}" id="filter-form">
                <div class="bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl p-5 space-y-6 sticky top-24 transition-colors">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E5E5EA] dark:border-[#30363d]">
                        <h3 class="font-semibold text-[#1D1D1F] dark:text-[#f0f6fc] text-sm">Bộ lọc tìm kiếm</h3>
                        @if(request()->hasAny(['keyword', 'category', 'price', 'level', 'sort']))
                            <a href="{{ url('/courses') }}" class="text-[11px] text-[#019D91] dark:text-[#02b3a5] hover:underline">
                                Đặt lại
                            </a>
                        @endif
                    </div>

                    {{-- Từ khóa --}}
                    <div>
                        <label class="block text-[11px] font-medium text-[#86868B] dark:text-[#8b949e] uppercase tracking-wider mb-2">Từ khóa</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                               placeholder="Tìm tên khóa học..."
                               class="w-full px-3.5 py-2 text-xs border border-[#E5E5EA] dark:border-[#30363d] rounded-xl bg-[#F5F5F7] dark:bg-[#0d1117] text-[#1D1D1F] dark:text-[#f0f6fc] focus:ring-1 focus:ring-[#019D91] dark:focus:border-[#02b3a5] outline-none transition">
                    </div>

                    {{-- Danh mục --}}
                    <div>
                        <label class="block text-[11px] font-medium text-[#86868B] dark:text-[#8b949e] uppercase tracking-wider mb-2">Danh mục</label>
                        <select name="category" class="w-full px-3 py-2 text-xs border border-[#E5E5EA] dark:border-[#30363d] rounded-xl bg-[#F5F5F7] dark:bg-[#0d1117] text-[#1D1D1F] dark:text-[#f0f6fc] focus:ring-1 focus:ring-[#019D91] dark:focus:border-[#02b3a5] outline-none transition">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->courses_count }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Giá --}}
                    <div>
                        <label class="block text-[11px] font-medium text-[#86868B] dark:text-[#8b949e] uppercase tracking-wider mb-2">Mức phí</label>
                        <div class="space-y-1.5">
                            @foreach([''=>'Tất cả', 'free'=>'Miễn phí', 'paid'=>'Có phí'] as $value => $label)
                            <label class="flex items-center space-x-2.5 cursor-pointer py-0.5">
                                <input type="radio" name="price" value="{{ $value }}"
                                       {{ request('price', '') === $value ? 'checked' : '' }}
                                       class="text-[#019D91] focus:ring-[#019D91] border-[#D2D2D7] dark:border-[#30363d] bg-white dark:bg-[#0d1117]">
                                <span class="text-xs text-[#1D1D1F] dark:text-[#c9d1d9]">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Trình độ --}}
                    <div>
                        <label class="block text-[11px] font-medium text-[#86868B] dark:text-[#8b949e] uppercase tracking-wider mb-2">Trình độ</label>
                        <div class="space-y-1.5">
                            @foreach(['' => 'Tất cả', 'beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'] as $value => $label)
                            <label class="flex items-center space-x-2.5 cursor-pointer py-0.5">
                                <input type="radio" name="level" value="{{ $value }}"
                                       {{ request('level', '') === $value ? 'checked' : '' }}
                                       class="text-[#019D91] focus:ring-[#019D91] border-[#D2D2D7] dark:border-[#30363d] bg-white dark:bg-[#0d1117]">
                                <span class="text-xs text-[#1D1D1F] dark:text-[#c9d1d9]">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sắp xếp --}}
                    <div>
                        <label class="block text-[11px] font-medium text-[#86868B] dark:text-[#8b949e] uppercase tracking-wider mb-2">Sắp xếp</label>
                        <select name="sort" class="w-full px-3 py-2 text-xs border border-[#E5E5EA] dark:border-[#30363d] rounded-xl bg-[#F5F5F7] dark:bg-[#0d1117] text-[#1D1D1F] dark:text-[#f0f6fc] focus:ring-1 focus:ring-[#019D91] dark:focus:border-[#02b3a5] outline-none transition">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="rating"  {{ request('sort') === 'rating'  ? 'selected' : '' }}>Đánh giá cao nhất</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit" class="btn-apple-primary w-full py-2 text-xs">
                            Áp dụng bộ lọc
                        </button>
                        <a href="{{ url('/courses') }}" class="btn-apple-secondary w-full py-2 text-xs text-center">
                            Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 min-w-0">
            {{-- Header info --}}
            <div class="flex items-end justify-between mb-6 pb-4 border-b border-[#E5E5EA] dark:border-[#30363d]">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-[#1D1D1F] dark:text-[#f0f6fc]">
                        Tất cả khóa học
                    </h1>
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e] mt-1">
                        Hiện có <span class="font-medium text-[#1D1D1F] dark:text-[#f0f6fc]">{{ $courses->total() }}</span> khóa học phù hợp
                        @if(request('keyword'))
                            với từ khóa "<span class="text-[#019D91] dark:text-[#02b3a5]">{{ request('keyword') }}</span>"
                        @endif
                    </p>
                </div>
            </div>

            {{-- Course Grid --}}
            @if($courses->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($courses as $course)
                        @include('partials.course-card', ['course' => $course])
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12 flex justify-center">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-24 text-center bg-[#F5F5F7] dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl p-8">
                    <div class="w-16 h-16 bg-white dark:bg-[#0d1117] border border-[#E5E5EA] dark:border-[#30363d] rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-[#86868B] dark:text-[#7d8590]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-[#1D1D1F] dark:text-[#f0f6fc] mb-1">Không tìm thấy khóa học nào</h3>
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e] max-w-sm mb-6">Hãy thử tìm với từ khóa khác hoặc điều chỉnh lại các tiêu chí bộ lọc</p>
                    <a href="{{ url('/courses') }}" class="btn-apple-primary px-5 py-2 text-xs">
                        Xóa tất cả bộ lọc
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
