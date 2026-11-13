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

    {{-- Filters Toolbar (Minimalist, gray inputs, horizontal divider) --}}
    <form method="GET" action="{{ route('admin.courses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <input type="hidden" name="status" value="{{ $status ?? 'all' }}">

            <div class="sm:col-span-2 relative">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tên khóa học hoặc giảng viên..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>

            @php
                $currentAdminCatId = (string) request('category_id', '');
                $selectedAdminCat = $categories->firstWhere('id', $currentAdminCatId);
                $adminCatFilterLabel = $selectedAdminCat ? $selectedAdminCat->name : 'Tất cả danh mục';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $currentAdminCatId }}',
                     selectedLabel: '{{ addslashes($adminCatFilterLabel) }}',
                     options: [
                         { value: '', label: 'Tất cả danh mục' },
                         @foreach($categories as $cat)
                             { value: '{{ $cat->id }}', label: '{{ addslashes($cat->name) }}' },
                         @endforeach
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <input type="hidden" name="category_id" :value="selectedValue">

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

    {{-- Courses Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="border-b border-gray-100 dark:border-neutral-800 text-gray-400 dark:text-neutral-500 text-xs font-semibold uppercase tracking-wider">
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
                                                    class="text-gray-400 dark:text-neutral-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center">
                                                <iconify-icon icon="lucide:check-circle" class="text-base"></iconify-icon>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Reject Button --}}
                                    @if($course->isPending() || $course->isPublished())
                                        <button type="button"
                                                @click="rejectCourseId = {{ $course->id }}; rejectCourseTitle = '{{ addslashes($course->title) }}'; rejectActionUrl = '{{ route('admin.courses.reject', $course) }}'; rejectModalOpen = true"
                                                title="{{ $course->isPublished() ? 'Từ chối (Vi phạm)' : 'Từ chối' }}"
                                                class="text-gray-400 dark:text-neutral-500 hover:text-red-600 dark:hover:text-red-400 transition-colors flex items-center">
                                            <iconify-icon icon="lucide:x-circle" class="text-base"></iconify-icon>
                                        </button>
                                    @endif

                                    {{-- Hide --}}
                                    @if($course->isPublished())
                                        <form method="POST" action="{{ route('admin.courses.hide', $course) }}" class="inline" data-confirm-message="Khóa học sẽ chuyển về Bản nháp. Tiếp tục?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Ẩn khóa học"
                                                    class="text-gray-400 dark:text-neutral-500 hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center">
                                                <iconify-icon icon="lucide:eye-off" class="text-base"></iconify-icon>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- View --}}
                                    @if($course->isPublished())
                                        <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                                           title="Xem trang công khai"
                                           class="text-gray-400 dark:text-neutral-500 hover:text-gray-900 dark:hover:text-white transition-colors flex items-center">
                                            <iconify-icon icon="lucide:eye" class="text-base"></iconify-icon>
                                        </a>
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                          data-confirm-message="CẢNH BÁO: Xóa khóa học sẽ xóa toàn bộ dữ liệu liên quan. Bạn có chắc?">
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
            <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Hiển thị {{ $courses->firstItem() }}–{{ $courses->lastItem() }} trong tổng số {{ $courses->total() }} khóa học
                </p>
                {{ $courses->links() }}
            </div>
        @endif

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
