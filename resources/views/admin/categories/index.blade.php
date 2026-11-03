@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page_title', 'Quản lý Danh Mục')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Danh sách Danh mục</h2>
            <p class="text-sm text-gray-500 mt-0.5">Phân loại các khóa học để người học dễ dàng tìm kiếm.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
            <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon>
            Thêm danh mục
        </a>
    </div>

    {{-- Search --}}
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tìm kiếm theo tên danh mục..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit"
                        class="flex-1 sm:flex-initial flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                    Tìm kiếm
                </button>
                @if(request('keyword'))
                    <a href="{{ route('admin.categories.index') }}"
                       class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-colors">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Tên danh mục</th>
                        <th class="px-4 py-4">Đường dẫn (Slug)</th>
                        <th class="px-4 py-4 text-center">Số khóa học</th>
                        <th class="px-4 py-4">Mô tả</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-[#019D91]/10 text-[#019D91] flex items-center justify-center shrink-0">
                                        <iconify-icon icon="lucide:tag" class="text-lg"></iconify-icon>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $category->name }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs font-mono text-gray-500">{{ $category->slug }}</td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.courses.index', ['category_id' => $category->id]) }}"
                                   class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-[#019D91]/10 hover:text-[#019D91] transition-colors">
                                    {{ $category->courses_count }} khóa
                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $category->description ?: '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       title="Chỉnh sửa"
                                       class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all">
                                        <iconify-icon icon="lucide:edit-3" class="text-lg"></iconify-icon>
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục &quot;{{ $category->name }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:trash-2" class="text-lg"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <iconify-icon icon="lucide:tag" class="text-gray-300 text-4xl"></iconify-icon>
                                    <p class="text-sm text-gray-500">Chưa có danh mục nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
