@extends('layouts.public')

@section('title', 'Danh sách Khóa học')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ===== SIDEBAR BỘ LỌC ===== --}}
        <aside class="w-full lg:w-64 shrink-0">
            <form method="GET" action="{{ url('/courses') }}" id="filter-form">
                <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-6 sticky top-20">
                    <h3 class="font-semibold text-gray-800 text-base">Bộ lọc</h3>

                    {{-- Từ khóa --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Từ khóa</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                               placeholder="Tìm kiếm khóa học..."
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-transparent outline-none">
                    </div>

                    {{-- Danh mục --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Danh mục</label>
                        <select name="category" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none bg-white">
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
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Giá</label>
                        <div class="space-y-2">
                            @foreach([''=>'Tất cả', 'free'=>'Miễn phí', 'paid'=>'Có phí'] as $value => $label)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="price" value="{{ $value }}"
                                       {{ request('price', '') === $value ? 'checked' : '' }}
                                       class="text-indigo-600 focus:ring-indigo-400">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Trình độ --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Trình độ</label>
                        <div class="space-y-2">
                            @foreach(['' => 'Tất cả', 'beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'] as $value => $label)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="level" value="{{ $value }}"
                                       {{ request('level', '') === $value ? 'checked' : '' }}
                                       class="text-indigo-600 focus:ring-indigo-400">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sắp xếp --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Sắp xếp</label>
                        <select name="sort" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none bg-white">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="rating"  {{ request('sort') === 'rating'  ? 'selected' : '' }}>Đánh giá cao</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Phổ biến nhất</option>
                        </select>
                    </div>

                    <div class="flex space-x-2 pt-1">
                        <button type="submit"
                                class="flex-1 bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Áp dụng
                        </button>
                        <a href="{{ url('/courses') }}"
                           class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                            Xóa lọc
                        </a>
                    </div>
                </div>
            </form>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 min-w-0">
            {{-- Tiêu đề + tổng số kết quả --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Danh sách Khóa học</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Tìm thấy <span class="font-semibold text-gray-800">{{ $courses->total() }}</span> khóa học
                        @if(request('keyword'))
                            cho từ khóa "<span class="text-indigo-600">{{ request('keyword') }}</span>"
                        @endif
                    </p>
                </div>
            </div>

            {{-- Danh sách khóa học --}}
            @if($courses->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($courses as $course)
                @include('partials.course-card', ['course' => $course])
                @endforeach
            </div>

            {{-- Phân trang --}}
            <div class="mt-10">
                {{ $courses->links() }}
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Không tìm thấy khóa học</h3>
                <p class="text-gray-400 text-sm mb-6">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                <a href="{{ url('/courses') }}" class="inline-block bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Xem tất cả khóa học
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
