@extends('layouts.admin')

@php $isEdit = $banner->exists; @endphp

@section('title', $isEdit ? 'Sửa banner' : 'Thêm banner')
@section('page_title', $isEdit ? 'Sửa banner' : 'Thêm banner')

@section('content')
<div class="max-w-2xl rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" class="space-y-5">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700">Tiêu đề</label>
            <input id="title" name="title" value="{{ old('title', $banner->title) }}" required autofocus class="mt-2 block w-full rounded-md border-gray-300">
        </div>

        <div>
            <label for="image" class="block text-sm font-semibold text-gray-700">Ảnh banner {{ $isEdit ? '(bỏ trống nếu giữ ảnh cũ)' : '' }}</label>
            @if($isEdit && $banner->image_path)
                <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" class="mt-2 h-28 rounded-lg object-cover">
            @endif
            <input id="image" type="file" name="image" accept="image/*" {{ $isEdit ? '' : 'required' }} class="mt-2 block w-full text-sm">
            <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF hoặc WEBP, tối đa 5MB. Nên dùng ảnh ngang tỉ lệ khoảng 3:1.</p>
        </div>

        <div>
            <label for="link_url" class="block text-sm font-semibold text-gray-700">Liên kết khi bấm vào (không bắt buộc)</label>
            <input id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="/courses" class="mt-2 block w-full rounded-md border-gray-300">
        </div>

        <div>
            <label for="order" class="block text-sm font-semibold text-gray-700">Thứ tự hiển thị (nhỏ hiện trước)</label>
            <input id="order" type="number" min="0" name="order" value="{{ old('order', $banner->order ?? 0) }}" class="mt-2 block w-full rounded-md border-gray-300">
        </div>

        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active)) class="rounded border-gray-300 text-rose-600 focus:ring-rose-500">
            Hiển thị banner này trên trang chủ
        </label>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-md bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-700">{{ $isEdit ? 'Lưu thay đổi' : 'Thêm banner' }}</button>
            <a href="{{ route('admin.banners.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Hủy</a>
        </div>
    </form>
</div>
@endsection
