@extends('layouts.admin')

@section('title', 'Quản lý thông báo')
@section('page_title', 'Quản lý Thông Báo')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Danh sách Thông báo</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Thông báo hệ thống hiển thị trên trang chủ và bảng điều khiển học viên.</p>
        </div>
        <a href="{{ route('admin.notifications.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
            <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon>
            Thêm thông báo
        </a>
    </div>

    {{-- Filters Toolbar (Minimalist, gray inputs, horizontal divider) --}}
    <form method="GET" action="{{ route('admin.notifications.index') }}" class="flex flex-col sm:flex-row items-center gap-3 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <div class="relative flex-1 w-full">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tìm theo tiêu đề hoặc nội dung thông báo..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>
            @php
                $notifTypeLabels = [
                    '' => 'Tất cả phân loại',
                    'system' => 'Hệ thống',
                    'course' => 'Khóa học',
                    'promotion' => 'Khuyến mãi',
                ];
                $currentNotifTypeVal = (string) request('type', '');
                $currentNotifTypeLabel = $notifTypeLabels[$currentNotifTypeVal] ?? 'Tất cả phân loại';
            @endphp
            <div class="relative w-full sm:w-48 shrink-0"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $currentNotifTypeVal }}',
                     selectedLabel: '{{ addslashes($currentNotifTypeLabel) }}',
                     options: [
                         { value: '', label: 'Tất cả phân loại' },
                         { value: 'system', label: 'Hệ thống' },
                         { value: 'course', label: 'Khóa học' },
                         { value: 'promotion', label: 'Khuyến mãi' },
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <input type="hidden" name="type" :value="selectedValue">

                <!-- Combobox Trigger Button -->
                <button type="button"
                        @click="open = !open"
                        :aria-expanded="open"
                        data-slot="combobox-trigger"
                        class="w-full h-11 px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm text-gray-700 dark:text-neutral-200 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
                    <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-neutral-200"></span>
                    <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-neutral-300 shrink-0 pointer-events-none"
                         :class="open ? 'rotate-180 text-gray-700 dark:text-neutral-200' : ''"
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
                     class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                    <template x-for="item in options" :key="item.value">
                        <button type="button"
                                @click="select(item.value, item.label)"
                                data-slot="combobox-item"
                                class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                :class="selectedValue === item.value 
                                    ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                    : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700/40'">
                            <span x-text="item.label"></span>
                            <svg x-show="selectedValue === item.value"
                                 class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none"
                                 fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit"
                        class="flex-1 sm:flex-initial flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                    Lọc
                </button>
                @if(request()->hasAny(['keyword', 'type']))
                    <a href="{{ route('admin.notifications.index') }}"
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
                    <th class="px-6 py-4">Tiêu đề</th>
                    <th class="px-4 py-4">Loại</th>
                    <th class="px-4 py-4">Nội dung</th>
                    <th class="px-4 py-4">Ngày tạo</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                @forelse($notifications as $notification)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                        <td class="px-6 py-4 max-w-xs">
                            <div class="flex items-center gap-2.5">
                                @if($notification->type === 'system')
                                    <iconify-icon icon="lucide:monitor" class="text-base text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
                                @elseif($notification->type === 'course')
                                    <iconify-icon icon="lucide:book-open" class="text-base text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
                                @else
                                    <iconify-icon icon="lucide:tag" class="text-base text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
                                @endif
                                <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate">{{ $notification->title }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($notification->type === 'system')
                                <span class="status-badge bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">Hệ thống</span>
                            @elseif($notification->type === 'course')
                                <span class="status-badge bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Khóa học</span>
                            @elseif($notification->type === 'promotion')
                                <span class="status-badge bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">Khuyến mãi</span>
                            @else
                                <span class="status-badge bg-gray-100 dark:bg-neutral-800 text-gray-500 dark:text-neutral-400">{{ $notification->type }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 max-w-md">
                            <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2">{{ \Illuminate\Support\Str::limit($notification->content, 120) }}</p>
                        </td>
                        <td class="px-4 py-4 text-xs text-gray-400 dark:text-neutral-500 whitespace-nowrap">
                            {{ $notification->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.notifications.edit', $notification) }}"
                                   title="Chỉnh sửa"
                                   class="text-gray-400 dark:text-neutral-500 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center">
                                    <iconify-icon icon="lucide:edit-3" class="text-base"></iconify-icon>
                                </a>
                                <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}"
                                      data-confirm-message="Bạn có chắc chắn muốn xóa thông báo này?">
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
                                <iconify-icon icon="lucide:bell" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                <p class="text-sm text-gray-500 dark:text-neutral-400">Chưa có thông báo nào</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notifications->hasPages())
        <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-neutral-400">
                Hiển thị {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} trong {{ $notifications->total() }} thông báo
            </p>
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
