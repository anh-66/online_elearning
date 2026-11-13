@extends('layouts.admin')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Sửa danh mục' : 'Thêm danh mục')
@section('page_title', $isEdit ? 'Sửa Danh Mục' : 'Thêm Danh Mục Mới')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">{{ $isEdit ? 'Cập nhật danh mục: ' . $category->name : 'Tạo danh mục mới' }}</h2>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">Đường dẫn thân thiện (slug) sẽ tự động tạo từ tên danh mục.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 flex items-center gap-1">
            <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
            Quay lại
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-5">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Tên danh mục <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="name" name="name" value="{{ old('name', $category->name) }}" required autofocus
                   placeholder="Ví dụ: Lập trình Web, Trí tuệ nhân tạo..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="icon" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Biểu tượng (Icon key)</label>
            <input id="icon" name="icon" value="{{ old('icon', $category->icon) }}"
                   placeholder="Ví dụ: code, chart-bar, palette, briefcase..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            <p class="mt-1 text-xs text-gray-400 dark:text-neutral-500">Các icon hỗ trợ: code, chart-bar, palette, briefcase, translate...</p>
            @error('icon') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Mô tả ngắn</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Mô tả tóm tắt về các khóa học thuộc danh mục này..."
                      class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all resize-none">{{ old('description', $category->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                {{ $isEdit ? 'Lưu thay đổi' : 'Thêm danh mục' }}
            </button>
            <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
