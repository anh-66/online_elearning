@extends('layouts.admin')

@section('title', 'Quản lý phản hồi & báo cáo sự cố')
@section('page_title', 'Phản Hồi & Báo Cáo Sự Cố')

@section('content')
<div class="space-y-6" x-data="{
    detailModalOpen: false,
    currentFeedback: null,
    updateActionUrl: '',
    openDetail(item) {
        this.currentFeedback = item;
        this.updateActionUrl = '/admin/feedbacks/' + item.id;
        this.detailModalOpen = true;
    }
}">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Danh sách Phản hồi & Báo cáo</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Tiếp nhận lỗi kỹ thuật, góp ý tính năng và thắc mắc từ học viên và khách truy cập.</p>
        </div>
    </div>

    {{-- Stats (Minimalist, raw icons, no colored box background) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:inbox" class="text-2xl text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Tổng phản hồi</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:clock" class="text-2xl text-amber-500 dark:text-amber-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Chờ xử lý</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ number_format($stats['pending']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:refresh-cw" class="text-2xl text-indigo-500 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Đang xử lý</p>
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($stats['in_progress']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:check-circle-2" class="text-2xl text-emerald-500 dark:text-emerald-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Đã giải quyết</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['resolved']) }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar (Minimalist, gray inputs, horizontal divider) --}}
    <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pb-6 border-b border-gray-100 dark:border-neutral-800">
        {{-- Keyword --}}
        <div class="lg:col-span-2 relative">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ $keyword }}"
                       placeholder="Tìm kiếm tiêu đề, nội dung, người gửi..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition">
            </div>

            {{-- Status --}}
            @php
                $fbStatusLabels = [
                    'all' => 'Tất cả trạng thái',
                    'pending' => 'Chờ xử lý',
                    'in_progress' => 'Đang xử lý',
                    'resolved' => 'Đã giải quyết',
                    'closed' => 'Đã đóng',
                ];
                $currentFbStatusLabel = $fbStatusLabels[$status] ?? 'Tất cả trạng thái';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $status }}',
                     selectedLabel: '{{ addslashes($currentFbStatusLabel) }}',
                     options: [
                         { value: 'all', label: 'Tất cả trạng thái' },
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
                <input type="hidden" name="status" :value="selectedValue">
                <button type="button" @click="open = !open" data-slot="combobox-trigger"
                        class="w-full h-10 px-3.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm text-gray-700 dark:text-neutral-200 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
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

            {{-- Category --}}
            @php
                $fbCatLabels = [
                    'all' => 'Tất cả loại',
                    'bug' => 'Lỗi kỹ thuật',
                    'feature' => 'Góp ý tính năng',
                    'question' => 'Câu hỏi',
                    'other' => 'Khác',
                ];
                $currentFbCatLabel = $fbCatLabels[$category] ?? 'Tất cả loại';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $category }}',
                     selectedLabel: '{{ addslashes($currentFbCatLabel) }}',
                     options: [
                         { value: 'all', label: 'Tất cả loại' },
                         { value: 'bug', label: 'Lỗi kỹ thuật' },
                         { value: 'feature', label: 'Góp ý tính năng' },
                         { value: 'question', label: 'Câu hỏi' },
                         { value: 'other', label: 'Khác' },
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <input type="hidden" name="category" :value="selectedValue">
                <button type="button" @click="open = !open" data-slot="combobox-trigger"
                        class="w-full h-10 px-3.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm text-gray-700 dark:text-neutral-200 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
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

            {{-- Priority & Submit --}}
            @php
                $fbPriorityLabels = [
                    'all' => 'Tất cả mức ưu tiên',
                    'high' => 'Cao',
                    'medium' => 'Trung bình',
                    'low' => 'Thấp',
                ];
                $currentFbPriorityLabel = $fbPriorityLabels[$priority] ?? 'Tất cả mức ưu tiên';
            @endphp
            <div class="flex items-center gap-2">
                <div class="relative flex-1"
                     x-data="{
                         open: false,
                         selectedValue: '{{ $priority }}',
                         selectedLabel: '{{ addslashes($currentFbPriorityLabel) }}',
                         options: [
                             { value: 'all', label: 'Tất cả mức ưu tiên' },
                             { value: 'high', label: 'Cao' },
                             { value: 'medium', label: 'Trung bình' },
                             { value: 'low', label: 'Thấp' },
                         ],
                         select(val, label) {
                             this.selectedValue = val;
                             this.selectedLabel = label;
                             this.open = false;
                         }
                     }">
                    <input type="hidden" name="priority" :value="selectedValue">
                    <button type="button" @click="open = !open" data-slot="combobox-trigger"
                            class="w-full h-10 px-3.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm text-gray-700 dark:text-neutral-200 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
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
                <button type="submit" class="px-4 py-2 bg-[#019D91] text-white text-sm font-medium rounded-lg hover:bg-[#008b80] transition shrink-0 h-10">
                    Lọc
                </button>
            </div>
    </form>

    {{-- Feedback Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-neutral-800 text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Người gửi</th>
                        <th class="py-3.5 px-4">Tiêu đề & Loại</th>
                        <th class="py-3.5 px-4">Ưu tiên</th>
                        <th class="py-3.5 px-4">Trạng thái</th>
                        <th class="py-3.5 px-4">Tệp</th>
                        <th class="py-3.5 px-4">Ngày gửi</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800 text-gray-700 dark:text-neutral-200">
                    @forelse($feedbacks as $item)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-neutral-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-xs text-gray-400 dark:text-neutral-500">#{{ $item->id }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-900 dark:text-neutral-100">{{ $item->sender_name ?? $item->user?->name ?? 'Khách' }}</div>
                                <div class="text-xs text-gray-400 dark:text-neutral-500">{{ $item->sender_email ?? $item->user?->email ?? 'Không có email' }}</div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="font-medium text-gray-900 dark:text-neutral-100 truncate" title="{{ $item->title }}">{{ $item->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-neutral-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    <span>{{ $item->category_label }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $item->priority_badge_class }}">
                                    {{ $item->priority_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $item->status_badge_class }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->attachment)
                                    <a href="{{ $item->attachment_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" title="Xem tệp đính kèm">
                                        <iconify-icon icon="lucide:paperclip" class="text-lg"></iconify-icon>
                                    </a>
                                @else
                                    <span class="text-gray-300 dark:text-neutral-600">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-500 dark:text-neutral-400">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <button type="button"
                                        @click="openDetail({{ json_encode([
                                            'id' => $item->id,
                                            'title' => $item->title,
                                            'category' => $item->category,
                                            'category_label' => $item->category_label,
                                            'priority' => $item->priority,
                                            'priority_label' => $item->priority_label,
                                            'priority_badge_class' => $item->priority_badge_class,
                                            'status' => $item->status,
                                            'status_label' => $item->status_label,
                                            'status_badge_class' => $item->status_badge_class,
                                            'sender_name' => $item->sender_name ?? $item->user?->name ?? 'Khách',
                                            'sender_email' => $item->sender_email ?? $item->user?->email ?? 'Không có email',
                                            'description' => $item->description,
                                            'attachment_url' => $item->attachment_url,
                                            'admin_notes' => $item->admin_notes,
                                            'created_at' => $item->created_at->format('d/m/Y H:i'),
                                            'resolved_at' => $item->resolved_at?->format('d/m/Y H:i'),
                                        ]) }})"
                                        class="px-3 py-1.5 bg-gray-100 dark:bg-neutral-800 hover:bg-[#019D91] hover:text-white text-gray-700 dark:text-neutral-200 text-xs font-medium rounded-lg transition">
                                    Chi tiết
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 dark:text-neutral-500">
                                <iconify-icon icon="lucide:inbox" class="text-4xl text-gray-300 dark:text-neutral-600 mb-2"></iconify-icon>
                                <p class="text-sm">Không tìm thấy phản hồi hoặc báo cáo sự cố nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
            <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>

    {{-- Detail & Action Modal --}}
    <div x-show="detailModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="detailModalOpen = false">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
             @click="detailModalOpen = false"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-neutral-900 rounded-2xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-gray-100 dark:border-neutral-800 space-y-6"
                 @click.stop>
                
                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 dark:border-neutral-800 pb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs text-gray-400 dark:text-neutral-500">#<span x-text="currentFeedback?.id"></span></span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200" x-text="currentFeedback?.category_label"></span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold border"
                                  :class="currentFeedback?.priority_badge_class"
                                  x-text="currentFeedback?.priority_label"></span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100" x-text="currentFeedback?.title"></h3>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-neutral-200 p-1">
                        <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                    </button>
                </div>

                {{-- Sender info --}}
                <div class="p-3 bg-gray-50 dark:bg-neutral-800/40 rounded-xl text-xs flex items-center justify-between text-gray-600 dark:text-neutral-300">
                    <div>
                        <span>Người gửi: </span>
                        <strong class="text-gray-900 dark:text-neutral-100" x-text="currentFeedback?.sender_name"></strong>
                        <span class="text-gray-400 dark:text-neutral-500"> - </span>
                        <span x-text="currentFeedback?.sender_email"></span>
                    </div>
                    <div class="text-gray-400 dark:text-neutral-500" x-text="currentFeedback?.created_at"></div>
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wider block">Mô tả sự cố / nội dung góp ý</label>
                    <div class="p-4 bg-gray-50/70 dark:bg-neutral-800/40 border border-gray-100 dark:border-neutral-800 rounded-xl text-sm text-gray-800 dark:text-neutral-200 leading-relaxed whitespace-pre-line"
                         x-text="currentFeedback?.description"></div>
                </div>

                {{-- Attachment --}}
                <template x-if="currentFeedback?.attachment_url">
                    <div class="flex items-center justify-between p-3.5 bg-blue-50/60 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl text-sm">
                        <div class="flex items-center gap-2 text-blue-900 dark:text-blue-200">
                            <iconify-icon icon="lucide:paperclip" class="text-lg text-blue-600 dark:text-blue-400"></iconify-icon>
                            <span class="font-medium text-xs">Có tệp đính kèm đi cùng báo cáo</span>
                        </div>
                        <a :href="currentFeedback?.attachment_url" target="_blank"
                           class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-1.5">
                            <iconify-icon icon="lucide:external-link"></iconify-icon>
                            Xem / Tải về
                        </a>
                    </div>
                </template>

                {{-- Update Status & Admin Notes Form --}}
                <form method="POST" :action="updateActionUrl" class="space-y-4 pt-2 border-t border-gray-100 dark:border-neutral-800">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200 mb-1">Cập nhật trạng thái</label>
                            <select name="status" x-model="currentFeedback.status"
                                    class="w-full px-3 py-2 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91]">
                                <option value="pending">Chờ xử lý</option>
                                <option value="in_progress">Đang xử lý</option>
                                <option value="resolved">Đã giải quyết</option>
                                <option value="closed">Đã đóng</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-200 mb-1">Ghi chú xử lý nội bộ (chỉ Admin thấy)</label>
                        <textarea name="admin_notes" rows="3" x-model="currentFeedback.admin_notes"
                                  placeholder="Ghi lại tiến độ xử lý hoặc phương án phản hồi cho học viên..."
                                  class="w-full px-3.5 py-2.5 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-lg hover:bg-[#008b80] transition shadow-sm">
                            Lưu cập nhật
                        </button>

                        <button type="button" @click="detailModalOpen = false" class="px-4 py-2 text-sm text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200">
                            Đóng
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>
@endsection
