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

    {{-- Filters --}}
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tìm theo tiêu đề hoặc nội dung thông báo..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>
            <select name="type"
                    class="w-full sm:w-48 px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] text-gray-600 dark:text-neutral-300 transition-all">
                <option value="">Tất cả phân loại</option>
                <option value="system" @selected(request('type') === 'system')>Hệ thống</option>
                <option value="course" @selected(request('type') === 'course')>Khóa học</option>
                <option value="promotion" @selected(request('type') === 'promotion')>Khuyến mãi</option>
            </select>
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
    </div>

    {{-- Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-neutral-800/30 text-gray-500 dark:text-neutral-400 text-xs font-semibold uppercase tracking-wider">
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
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                                        @if($notification->type === 'system') bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400
                                        @elseif($notification->type === 'course') bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                        @elseif($notification->type === 'promotion') bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400
                                        @else bg-gray-100 dark:bg-neutral-800 text-gray-500 dark:text-neutral-400 @endif">
                                        @if($notification->type === 'system')
                                            <iconify-icon icon="lucide:monitor" class="text-lg"></iconify-icon>
                                        @elseif($notification->type === 'course')
                                            <iconify-icon icon="lucide:book-open" class="text-lg"></iconify-icon>
                                        @else
                                            <iconify-icon icon="lucide:tag" class="text-lg"></iconify-icon>
                                        @endif
                                    </div>
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.notifications.edit', $notification) }}"
                                       title="Chỉnh sửa"
                                       class="p-2 text-gray-400 dark:text-neutral-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-all">
                                        <iconify-icon icon="lucide:edit-3" class="text-lg"></iconify-icon>
                                    </a>
                                    <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa thông báo này?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa"
                                                class="p-2 text-gray-400 dark:text-neutral-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all">
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
            <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/20 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Hiển thị {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} trong {{ $notifications->total() }} thông báo
                </p>
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
