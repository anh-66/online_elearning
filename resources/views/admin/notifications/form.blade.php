@extends('layouts.admin')

@php $isEdit = $notification->exists; @endphp

@section('title', $isEdit ? 'Sửa thông báo' : 'Thêm thông báo')
@section('page_title', $isEdit ? 'Sửa Thông Báo' : 'Thêm Thông Báo Mới')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">{{ $isEdit ? 'Chỉnh sửa thông báo' : 'Tạo thông báo mới' }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">Thông báo sẽ xuất hiện trên trang chủ và bảng điều khiển của tất cả học viên.</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
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
            <label for="title" class="block text-sm font-semibold text-gray-700">Tiêu đề thông báo <span class="text-red-500">*</span></label>
            <input id="title" name="title" value="{{ old('title', $notification->title) }}" required autofocus
                   placeholder="Ví dụ: Lịch bảo trì hệ thống, Khóa học mới ra mắt..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        @php
            $currentNotifType = old('type', $notification->type ?? 'system');
            $typeLabels = [
                'system' => 'Hệ thống (System)',
                'course' => 'Khóa học (Course)',
                'promotion' => 'Khuyến mãi (Promotion)',
            ];
            $currentTypeLabel = $typeLabels[$currentNotifType] ?? 'Hệ thống (System)';
        @endphp
        <div class="relative"
             x-data="{
                 open: false,
                 selectedValue: '{{ $currentNotifType }}',
                 selectedLabel: '{{ $currentTypeLabel }}',
                 options: [
                     { value: 'system', label: 'Hệ thống (System)' },
                     { value: 'course', label: 'Khóa học (Course)' },
                     { value: 'promotion', label: 'Khuyến mãi (Promotion)' },
                 ],
                 select(val, label) {
                     this.selectedValue = val;
                     this.selectedLabel = label;
                     this.open = false;
                 }
             }">
            <label class="block text-sm font-semibold text-gray-700">Phân loại <span class="text-red-500">*</span></label>
            <input type="hidden" name="type" :value="selectedValue">

            <!-- Combobox Trigger Button -->
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    data-slot="combobox-trigger"
                    class="mt-1.5 w-full h-11 px-3.5 rounded-xl border border-gray-200 bg-white text-gray-900 text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
                <span x-text="selectedLabel" class="truncate font-medium text-gray-800"></span>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 shrink-0 pointer-events-none"
                     :class="open ? 'rotate-180 text-gray-700' : ''"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                </svg>
            </button>

            <!-- Combobox Content Popup -->
            <div x-show="open"
                 x-cloak
                 @click.outside="open = false"
                 @keydown.escape.window="open = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                 data-slot="combobox-content"
                 class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 bg-white shadow-xl ring-1 ring-black/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                <template x-for="item in options" :key="item.value">
                    <button type="button"
                            @click="select(item.value, item.label)"
                            data-slot="combobox-item"
                            class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                            :class="selectedValue === item.value 
                                ? 'bg-neutral-100 text-neutral-900 font-medium' 
                                : 'text-neutral-700 hover:bg-neutral-50'">
                        <span x-text="item.label"></span>
                        <svg x-show="selectedValue === item.value"
                             class="w-4 h-4 text-neutral-900 shrink-0 pointer-events-none"
                             fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </template>
            </div>
            @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-semibold text-gray-700">Nội dung thông báo <span class="text-red-500">*</span></label>
            <textarea id="content" name="content" rows="5" required
                      placeholder="Nhập đầy đủ chi tiết thông báo gửi đến người dùng..."
                      class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all resize-none">{{ old('content', $notification->content) }}</textarea>
            @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                {{ $isEdit ? 'Lưu thay đổi' : 'Đăng thông báo' }}
            </button>
            <a href="{{ route('admin.notifications.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
