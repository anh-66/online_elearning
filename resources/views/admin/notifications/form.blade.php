@extends('layouts.admin')

@php $isEdit = $notification->exists; @endphp

@section('title', $isEdit ? 'Sửa thông báo' : 'Thêm thông báo')
@section('page_title', $isEdit ? 'Sửa Thông Báo' : 'Thêm Thông Báo Mới')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">{{ $isEdit ? 'Chỉnh sửa thông báo' : 'Tạo thông báo mới' }}</h2>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">Thông báo sẽ xuất hiện trên trang chủ và bảng điều khiển của tất cả học viên.</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="text-xs font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 flex items-center gap-1">
            <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
            Quay lại
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.notifications.update', $notification) : route('admin.notifications.store') }}" class="space-y-5">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Tiêu đề thông báo <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="title" name="title" value="{{ old('title', $notification->title) }}" required autofocus
                   placeholder="Ví dụ: Lịch bảo trì hệ thống, Khóa học mới ra mắt..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('title') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Phân loại <span class="text-red-500 dark:text-red-400">*</span></label>
            <select id="type" name="type" required
                    class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
                <option value="system" @selected(old('type', $notification->type) === 'system')>Hệ thống (System)</option>
                <option value="course" @selected(old('type', $notification->type) === 'course')>Khóa học (Course)</option>
                <option value="promotion" @selected(old('type', $notification->type) === 'promotion')>Khuyến mãi (Promotion)</option>
            </select>
            @error('type') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Nội dung thông báo <span class="text-red-500 dark:text-red-400">*</span></label>
            <textarea id="content" name="content" rows="5" required
                      placeholder="Nhập đầy đủ chi tiết thông báo gửi đến người dùng..."
                      class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all resize-none">{{ old('content', $notification->content) }}</textarea>
            @error('content') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                {{ $isEdit ? 'Lưu thay đổi' : 'Đăng thông báo' }}
            </button>
            <a href="{{ route('admin.notifications.index') }}" class="text-sm font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
