@extends('layouts.admin')

@php $isEdit = $banner->exists; @endphp

@section('title', $isEdit ? 'Sửa banner' : 'Thêm banner')
@section('page_title', $isEdit ? 'Sửa Banner' : 'Thêm Banner Mới')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">{{ $isEdit ? 'Chỉnh sửa banner' : 'Thêm banner mới' }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">Banner sẽ hiển thị trên slider đầu trang chủ để quảng bá khóa học.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
            Quay lại
        </a>
    </div>

    <form method="POST" enctype="multipart/form-data"
          action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" class="space-y-5">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700">Tiêu đề <span class="text-red-500">*</span></label>
            <input id="title" name="title" value="{{ old('title', $banner->title) }}" required autofocus
                   placeholder="Ví dụ: Khóa học Lập trình Web Fullstack 2026..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="image" class="block text-sm font-semibold text-gray-700">Ảnh banner {{ $isEdit ? '(bỏ trống nếu giữ ảnh cũ)' : '*' }}</label>
            @if($isEdit && $banner->image_path)
                <div class="mt-2 mb-3">
                    <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" class="h-28 rounded-xl object-cover border border-gray-200 shadow-sm">
                </div>
            @endif
            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" {{ $isEdit ? '' : 'required' }}
                   class="mt-1.5 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#019D91]/10 file:text-[#019D91] hover:file:bg-[#019D91]/20">
            <p class="mt-1 text-xs text-gray-400">Định dạng: JPG, PNG, GIF, WEBP tối đa 5MB. Khuyên dùng tỉ lệ 3:1 hoặc 16:9.</p>
            @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="link_url" class="block text-sm font-semibold text-gray-700">Liên kết URL khi bấm vào (tùy chọn)</label>
            <input id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="/courses hoặc https://..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('link_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="order" class="block text-sm font-semibold text-gray-700">Thứ tự hiển thị (số nhỏ xếp trước)</label>
            <input id="order" type="number" min="0" max="9999" name="order" value="{{ old('order', $banner->order ?? 0) }}"
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active))
                   class="rounded border-gray-300 text-[#019D91] focus:ring-[#019D91]">
            Hiển thị banner này trên slider trang chủ
        </label>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                {{ $isEdit ? 'Lưu thay đổi' : 'Thêm banner' }}
            </button>
            <a href="{{ route('admin.banners.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
