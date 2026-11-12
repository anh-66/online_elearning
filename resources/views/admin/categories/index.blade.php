@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page_title', 'Quản lý Danh Mục')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Danh sách Danh mục</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Phân loại các khóa học để người học dễ dàng tìm kiếm.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
            <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon>
            Thêm danh mục
        </a>
    </div>

    {{-- Search Toolbar (Minimalist, gray inputs, horizontal divider) --}}
    <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row items-center gap-3 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <div class="relative flex-1 w-full">
            <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
            <input type="text" name="keyword" value="{{ request('keyword') }}"
                   placeholder="Tìm kiếm theo tên danh mục..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit"
                    class="flex-1 sm:flex-initial flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                Tìm kiếm
            </button>
            @if(request('keyword'))
                <a href="{{ route('admin.categories.index') }}"
                   class="rounded-xl bg-gray-100 dark:bg-neutral-800 px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                    Đặt lại
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="border-b border-gray-100 dark:border-neutral-800 text-gray-400 dark:text-neutral-500 text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Tên danh mục</th>
                    <th class="px-4 py-4">Đường dẫn (Slug)</th>
                    <th class="px-4 py-4 text-center">Số khóa học</th>
                    <th class="px-4 py-4">Mô tả</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <iconify-icon icon="lucide:tag" class="text-base text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
                                <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100">{{ $category->name }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-xs font-mono text-gray-500 dark:text-neutral-400">{{ $category->slug }}</td>
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.courses.index', ['category_id' => $category->id]) }}"
                               class="inline-flex rounded-full bg-gray-100 dark:bg-neutral-800 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:text-neutral-200 hover:bg-[#019D91]/10 hover:text-[#019D91] dark:hover:text-[#02b3a5] transition-colors">
                                {{ $category->courses_count }} khóa
                            </a>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-neutral-400 max-w-xs truncate">{{ $category->description ?: '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   title="Chỉnh sửa"
                                   class="text-gray-400 dark:text-neutral-500 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center">
                                    <iconify-icon icon="lucide:edit-3" class="text-base"></iconify-icon>
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa danh mục &quot;{{ $category->name }}&quot;?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa"
                                            class="text-gray-400 dark:text-neutral-500 hover:text-red-600 dark:hover:text-red-400 transition-colors flex items-center">
                                        <iconify-icon icon="lucide:trash-2" class="text-base"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <iconify-icon icon="lucide:tag" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                <p class="text-sm text-gray-500 dark:text-neutral-400">Chưa có danh mục nào</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-neutral-400">
                Hiển thị {{ $categories->firstItem() }}–{{ $categories->lastItem() }} trong {{ $categories->total() }} danh mục
            </p>
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
