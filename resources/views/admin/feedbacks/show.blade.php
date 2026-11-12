@extends('layouts.admin')

@section('title', 'Chi tiết phản hồi #' . $feedback->id)
@section('page_title', 'Chi Tiết Phản Hồi #' . $feedback->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.feedbacks.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white transition">
            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
            <span>Quay lại danh sách</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $feedback->status_badge_class }}">
                {{ $feedback->status_label }}
            </span>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $feedback->priority_badge_class }}">
                Ưu tiên: {{ $feedback->priority_label }}
            </span>
        </div>
    </div>

    <div class="glass-card p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-gray-400 dark:text-neutral-500">
                <span>Loại: {{ $feedback->category_label }}</span>
                <span>&bull;</span>
                <span>Ngày gửi: {{ $feedback->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $feedback->title }}</h2>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-neutral-800/40 rounded-xl flex items-center justify-between text-sm">
            <div>
                <span class="text-gray-500 dark:text-neutral-400">Người gửi: </span>
                <strong class="text-gray-900 dark:text-neutral-100">{{ $feedback->sender_name ?? $feedback->user?->name ?? 'Khách' }}</strong>
                @if($feedback->sender_email || $feedback->user?->email)
                    <span class="text-gray-400 dark:text-neutral-500"> ({{ $feedback->sender_email ?? $feedback->user?->email }})</span>
                @endif
            </div>

            @if($feedback->user)
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-300">Thành viên hệ thống</span>
            @else
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-gray-200 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300">Khách</span>
            @endif
        </div>

        <div class="space-y-2">
            <h4 class="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Nội dung chi tiết</h4>
            <div class="p-5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-100 dark:border-neutral-800 rounded-xl text-sm leading-relaxed text-gray-800 dark:text-neutral-200 whitespace-pre-line">
                {{ $feedback->description }}
            </div>
        </div>

        @if($feedback->attachment)
            <div class="p-4 bg-blue-50/60 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-2 text-blue-900 dark:text-blue-200 text-sm">
                    <iconify-icon icon="lucide:paperclip" class="text-xl text-blue-600 dark:text-blue-400"></iconify-icon>
                    <span>Có tệp đính kèm đi cùng báo cáo</span>
                </div>
                <a href="{{ $feedback->attachment_url }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition inline-flex items-center gap-1.5">
                    <iconify-icon icon="lucide:download"></iconify-icon>
                    Tải về / Xem tệp
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.feedbacks.update', $feedback) }}" class="space-y-4 pt-4 border-t border-gray-100 dark:border-neutral-800">
            @csrf
            @method('PATCH')

            <h4 class="text-sm font-bold text-gray-900 dark:text-neutral-100">Xử lý phản hồi</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $showStatusLabels = [
                        'pending' => 'Chờ xử lý',
                        'in_progress' => 'Đang xử lý',
                        'resolved' => 'Đã giải quyết',
                        'closed' => 'Đã đóng',
                    ];
                    $currentShowStatusLabel = $showStatusLabels[$feedback->status] ?? 'Chờ xử lý';
                @endphp
                <div class="relative"
                     x-data="{
                         open: false,
                         selectedValue: '{{ $feedback->status }}',
                         selectedLabel: '{{ $currentShowStatusLabel }}',
                         options: [
                             { value: 'pending', label: 'Chờ xử lý' },
                             { value: 'in_progress', label: 'Đang xử lý' },
                             { value: 'resolved', label: 'Đã giải quyết' },
                             { value: 'closed', label: 'Đã đóng' },
                         ],
                         select(val, label) {
                             this.selectedValue = val;
                             this.selectedLabel = label;
                             this.open = false;
                         }
                     }">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200 mb-1">Trạng thái xử lý</label>
                    <input type="hidden" name="status" :value="selectedValue">
                    <button type="button" @click="open = !open" data-slot="combobox-trigger"
                            class="w-full h-10 px-3.5 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm text-gray-700 dark:text-neutral-200 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
                        <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-neutral-200"></span>
                        <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-neutral-300 shrink-0 pointer-events-none"
                             :class="open ? 'rotate-180 text-gray-700 dark:text-neutral-200' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false" @keydown.escape.window="open = false"
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1 scale-98" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         data-slot="combobox-content" class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                        <template x-for="item in options" :key="item.value">
                            <button type="button" @click="select(item.value, item.label)" data-slot="combobox-item"
                                    class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                    :class="selectedValue === item.value ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700/40'">
                                <span x-text="item.label"></span>
                                <svg x-show="selectedValue === item.value" class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200 mb-1">Ghi chú nội bộ</label>
                <textarea name="admin_notes" rows="3" placeholder="Ghi chú thêm về việc xử lý lỗi hoặc giải đáp cho người học..."
                          class="w-full px-3.5 py-2.5 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] resize-none">{{ old('admin_notes', $feedback->admin_notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-lg hover:bg-[#008b80] transition shadow-sm">
                    Lưu cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
