@extends('layouts.admin')

@section('title', 'Quản lý khóa học')
@section('page_title', 'Quản lý & Duyệt Khóa Học')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, rejectCourseId: null, rejectCourseTitle: '', rejectActionUrl: '' }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Danh sách Khóa học</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Kiểm duyệt nội dung, duyệt phát hành và xử lý vi phạm.</p>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="{{ route('admin.courses.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all
                  {{ ($status ?? 'all') === 'all' ? 'bg-gray-900 text-white shadow-sm dark:bg-white dark:text-neutral-900' : 'bg-white dark:bg-neutral-900 text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800 border border-gray-200 dark:border-neutral-800' }}">
            Tất cả
        </a>
        <a href="{{ route('admin.courses.index', array_merge(request()->except('status', 'page'), ['status' => \App\Models\Course::STATUS_PENDING])) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all
                  {{ ($status ?? '') === \App\Models\Course::STATUS_PENDING ? 'bg-amber-500 text-white shadow-sm' : 'bg-white dark:bg-neutral-900 text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800 border border-gray-200 dark:border-neutral-800' }}">
            Chờ duyệt ({{ $statusCounts[\App\Models\Course::STATUS_PENDING] ?? 0 }})
        </a>
        <a href="{{ route('admin.courses.index', array_merge(request()->except('status', 'page'), ['status' => \App\Models\Course::STATUS_PUBLISHED])) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all
                  {{ ($status ?? '') === \App\Models\Course::STATUS_PUBLISHED ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-neutral-900 text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800 border border-gray-200 dark:border-neutral-800' }}">
            Đã xuất bản ({{ $statusCounts[\App\Models\Course::STATUS_PUBLISHED] ?? 0 }})
        </a>
        <a href="{{ route('admin.courses.index', array_merge(request()->except('status', 'page'), ['status' => \App\Models\Course::STATUS_DRAFT])) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all
                  {{ ($status ?? '') === \App\Models\Course::STATUS_DRAFT ? 'bg-gray-600 text-white shadow-sm' : 'bg-white dark:bg-neutral-900 text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800 border border-gray-200 dark:border-neutral-800' }}">
            Bản nháp ({{ $statusCounts[\App\Models\Course::STATUS_DRAFT] ?? 0 }})
        </a>
        <a href="{{ route('admin.courses.index', array_merge(request()->except('status', 'page'), ['status' => \App\Models\Course::STATUS_REJECTED])) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all
                  {{ ($status ?? '') === \App\Models\Course::STATUS_REJECTED ? 'bg-red-600 text-white shadow-sm' : 'bg-white dark:bg-neutral-900 text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800 border border-gray-200 dark:border-neutral-800' }}">
            Bị từ chối ({{ $statusCounts[\App\Models\Course::STATUS_REJECTED] ?? 0 }})
        </a>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('admin.courses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="status" value="{{ $status ?? 'all' }}">

            <div class="sm:col-span-2 relative">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tên khóa học hoặc giảng viên..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>

            <select name="category_id"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] text-gray-600 dark:text-neutral-300 transition-all">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string)request('category_id') === (string)$cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                    Lọc
                </button>
                @if(request()->hasAny(['keyword', 'category_id']))
                    <a href="{{ route('admin.courses.index', ['status' => $status ?? 'all']) }}"
                       class="rounded-xl bg-gray-100 dark:bg-neutral-800 px-3.5 py-2.5 text-sm font-semibold text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Courses Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-neutral-800/30 text-gray-500 dark:text-neutral-400 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Khóa học</th>
                        <th class="px-4 py-4">Giảng viên</th>
                        <th class="px-4 py-4">Danh mục</th>
                        <th class="px-4 py-4 text-center">Ghi danh</th>
                        <th class="px-4 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors group">
                            {{-- Course Thumbnail + Title --}}
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-16 h-11 rounded-lg bg-gray-100 dark:bg-neutral-800 overflow-hidden shrink-0 border border-gray-200 dark:border-neutral-800">
                                        @if($course->thumbnail)
                                            <img src="{{ Storage::disk('public')->url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <iconify-icon icon="lucide:book-open" class="text-gray-400 dark:text-neutral-500 text-xl"></iconify-icon>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 max-w-xs">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 leading-snug">{{ $course->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                                            {{ $course->price > 0 ? number_format($course->price).' đ' : 'Miễn phí' }}
                                        </p>
                                        @if($course->status === \App\Models\Course::STATUS_REJECTED && $course->reject_reason)
                                            <div class="mt-1.5 p-2 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-100 dark:border-red-500/20 text-xs text-red-700 dark:text-red-300">
                                                <span class="font-semibold">Lý do từ chối:</span> {{ $course->reject_reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Instructor --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $course->instructor->name ?? '—' }}</p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $course->instructor->email ?? '' }}</p>
                            </td>

                            {{-- Category --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-xs font-medium text-[#019D91] dark:text-[#02b3a5] bg-[#019D91]/10 px-2.5 py-1 rounded-lg">
                                    {{ $course->category->name ?? 'Chưa phân loại' }}
                                </span>
                            </td>

                            {{-- Enrollments --}}
                            <td class="px-4 py-4 text-center font-semibold text-gray-900 dark:text-neutral-100 whitespace-nowrap">
                                {{ number_format($course->enrollments_count ?? 0) }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($course->status === \App\Models\Course::STATUS_PUBLISHED)
                                    <span class="status-badge bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Đã xuất bản</span>
                                @elseif($course->status === \App\Models\Course::STATUS_PENDING)
                                    <span class="status-badge bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">Chờ duyệt</span>
                                @elseif($course->status === \App\Models\Course::STATUS_REJECTED)
                                    <span class="status-badge bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400">Bị từ chối</span>
                                @else
                                    <span class="status-badge bg-gray-100 dark:bg-neutral-800 text-gray-500 dark:text-neutral-400">Bản nháp</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Approve --}}
                                    @if($course->isPending())
                                        <form method="POST" action="{{ route('admin.courses.approve', $course) }}" class="inline" data-confirm-message="Xác nhận duyệt và xuất bản khóa học này?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Duyệt"
                                                    class="p-2 text-gray-400 dark:text-neutral-500 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 rounded-lg transition-all">
                                                <iconify-icon icon="lucide:check-circle" class="text-lg"></iconify-icon>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject Button --}}
                                    @if($course->isPending() || $course->isPublished())
                                        <button type="button"
                                                @click="rejectCourseId = {{ $course->id }}; rejectCourseTitle = '{{ addslashes($course->title) }}'; rejectActionUrl = '{{ route('admin.courses.reject', $course) }}'; rejectModalOpen = true"
                                                title="{{ $course->isPublished() ? 'Từ chối (Vi phạm)' : 'Từ chối' }}"
                                                class="p-2 text-gray-400 dark:text-neutral-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:x-circle" class="text-lg"></iconify-icon>
                                        </button>
                                    @endif

                                    {{-- Hide --}}
                                    @if($course->isPublished())
                                        <form method="POST" action="{{ route('admin.courses.hide', $course) }}" class="inline" data-confirm-message="Khóa học sẽ chuyển về Bản nháp. Tiếp tục?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Ẩn khóa học"
                                                    class="p-2 text-gray-400 dark:text-neutral-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-all">
                                                <iconify-icon icon="lucide:eye-off" class="text-lg"></iconify-icon>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- View --}}
                                    @if($course->isPublished())
                                        <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                                           title="Xem trang công khai"
                                           class="p-2 text-gray-400 dark:text-neutral-500 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:bg-[#019D91]/10 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:eye" class="text-lg"></iconify-icon>
                                        </a>
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                          data-confirm-message="CẢNH BÁO: Xóa khóa học sẽ xóa toàn bộ dữ liệu liên quan. Bạn có chắc?">
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
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <iconify-icon icon="lucide:book-open" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400">Không tìm thấy khóa học phù hợp</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($courses->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/20 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Hiển thị {{ $courses->firstItem() }}–{{ $courses->lastItem() }} trong tổng số {{ $courses->total() }} khóa học
                </p>
                {{ $courses->links() }}
            </div>
        @endif
    </div>

    {{-- Reject Modal --}}
    <div x-show="rejectModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="rejectModalOpen" @click="rejectModalOpen = false"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <div x-show="rejectModalOpen"
                 class="relative glass-card p-6 shadow-2xl w-full max-w-lg z-10">
                <form :action="rejectActionUrl" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-neutral-800">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                                <iconify-icon icon="lucide:x-circle" class="text-red-500 dark:text-red-400 text-lg"></iconify-icon>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-neutral-100" id="modal-title">Từ chối / Khóa vi phạm</h3>
                        </div>
                        <button type="button" @click="rejectModalOpen = false"
                                class="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-lg transition-all">
                            <iconify-icon icon="lucide:x" class="text-lg"></iconify-icon>
                        </button>
                    </div>

                    <div class="mt-5">
                        <p class="text-sm text-gray-600 dark:text-neutral-300">
                            Khóa học: <span class="font-semibold text-gray-900 dark:text-neutral-100" x-text="rejectCourseTitle"></span>
                        </p>
                        <label for="reject_reason" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mt-4 mb-1.5">
                            Lý do từ chối <span class="text-red-500 dark:text-red-400">*</span>
                        </label>
                        <textarea id="reject_reason" name="reject_reason" rows="4" required
                                  placeholder="Nhập chi tiết lý do từ chối hoặc nội dung vi phạm..."
                                  class="w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all resize-none"></textarea>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="rejectModalOpen = false"
                                class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-neutral-800 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                            Hủy
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                            Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
