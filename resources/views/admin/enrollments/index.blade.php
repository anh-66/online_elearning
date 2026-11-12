@extends('layouts.admin')

@section('title', 'Quản lý ghi danh')
@section('page_title', 'Quản lý Ghi Danh')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Danh sách Ghi danh</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Theo dõi lượt tham gia học tập của học viên vào các khóa học.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2 relative">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tên học viên, email hoặc tên khóa học..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>

            <select name="status"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] text-gray-600 dark:text-neutral-300 transition-all">
                <option value="">Tất cả (Miễn phí & Trả phí)</option>
                <option value="{{ \App\Models\Enrollment::STATUS_FREE }}" @selected(request('status') === \App\Models\Enrollment::STATUS_FREE)>Miễn phí</option>
                <option value="{{ \App\Models\Enrollment::STATUS_PAID }}" @selected(request('status') === \App\Models\Enrollment::STATUS_PAID)>Đã thanh toán</option>
            </select>

            <div class="sm:col-span-3 flex justify-end gap-2">
                <button type="submit"
                        class="flex items-center gap-2 rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                    Lọc dữ liệu
                </button>
                @if(request()->hasAny(['keyword', 'status']))
                    <a href="{{ route('admin.enrollments.index') }}"
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
                        <th class="px-6 py-4">Học viên</th>
                        <th class="px-4 py-4">Khóa học</th>
                        <th class="px-4 py-4">Hình thức</th>
                        <th class="px-4 py-4">Ngày ghi danh</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100">{{ $enrollment->user->name ?? 'Người dùng đã xóa' }}</p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $enrollment->user->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-4 max-w-xs">
                                <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 truncate">{{ $enrollment->course->title ?? 'Khóa học đã xóa' }}</p>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($enrollment->status === \App\Models\Enrollment::STATUS_PAID)
                                    <span class="status-badge bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Đã thanh toán</span>
                                @else
                                    <span class="status-badge bg-[#019D91]/10 text-[#019D91] dark:text-[#02b3a5]">Miễn phí</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                                {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d/m/Y H:i') : ($enrollment->created_at?->format('d/m/Y H:i') ?? '—') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}"
                                          data-confirm-message="Bạn có chắc muốn hủy lượt ghi danh này?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hủy ghi danh"
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
                                    <iconify-icon icon="lucide:clipboard-list" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400">Chưa có lượt ghi danh nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/20 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Hiển thị {{ $enrollments->firstItem() }}–{{ $enrollments->lastItem() }} trong {{ $enrollments->total() }} ghi danh
                </p>
                {{ $enrollments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
