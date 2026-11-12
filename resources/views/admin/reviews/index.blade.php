@extends('layouts.admin')

@section('title', 'Kiểm duyệt đánh giá & bình luận')
@section('page_title', 'Kiểm Duyệt Đánh Giá & Bình Luận')

@section('content')
<div class="space-y-6" x-data="{
    detailModalOpen: false,
    modalType: 'review',
    modalUser: '',
    modalEmail: '',
    modalCourse: '',
    modalLesson: '',
    modalRating: null,
    modalTime: '',
    modalContent: ''
}">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Kiểm duyệt Nội dung</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Xóa các đánh giá hoặc bình luận có nội dung vi phạm cộng đồng. Bấm vào nội dung để xem chi tiết đầy đủ.</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 border-b border-gray-200 dark:border-neutral-800">
        <a href="{{ route('admin.reviews.index', ['tab' => 'reviews']) }}"
           class="px-5 py-3 text-sm font-semibold border-b-2 transition-all
                  {{ ($tab ?? 'reviews') === 'reviews' ? 'border-[#019D91] text-[#019D91] dark:text-[#02b3a5]' : 'border-transparent text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200' }}">
            <span class="flex items-center gap-2">
                <iconify-icon icon="lucide:star" class="text-base"></iconify-icon>
                Đánh giá Khóa học
            </span>
        </a>
        <a href="{{ route('admin.reviews.index', ['tab' => 'comments']) }}"
           class="px-5 py-3 text-sm font-semibold border-b-2 transition-all
                  {{ ($tab ?? '') === 'comments' ? 'border-[#019D91] text-[#019D91] dark:text-[#02b3a5]' : 'border-transparent text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200' }}">
            <span class="flex items-center gap-2">
                <iconify-icon icon="lucide:message-square" class="text-base"></iconify-icon>
                Bình luận Bài học
            </span>
        </a>
    </div>

    @if(($tab ?? 'reviews') === 'reviews')
        {{-- Reviews Filters --}}
        <div class="glass-card p-4">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <input type="hidden" name="tab" value="reviews">
                <div class="sm:col-span-2 relative">
                    <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                           placeholder="Nội dung đánh giá, tên người học hoặc tên khóa học..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
                </div>
                <select name="rating"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] text-gray-600 dark:text-neutral-300 transition-all">
                    <option value="">Tất cả mức sao (1–5)</option>
                    <option value="5" @selected(request('rating') === '5')>5 sao ⭐⭐⭐⭐⭐</option>
                    <option value="4" @selected(request('rating') === '4')>4 sao ⭐⭐⭐⭐</option>
                    <option value="3" @selected(request('rating') === '3')>3 sao ⭐⭐⭐</option>
                    <option value="2" @selected(request('rating') === '2')>2 sao ⭐⭐</option>
                    <option value="1" @selected(request('rating') === '1')>1 sao ⭐</option>
                </select>
                <div class="sm:col-span-3 flex justify-end gap-2">
                    <button type="submit"
                            class="flex items-center gap-2 rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                        <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                        Lọc đánh giá
                    </button>
                    @if(request()->hasAny(['keyword', 'rating']))
                        <a href="{{ route('admin.reviews.index', ['tab' => 'reviews']) }}"
                           class="rounded-xl bg-gray-100 dark:bg-neutral-800 px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                            Đặt lại
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Reviews Table --}}
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 dark:bg-neutral-800/30 text-gray-500 dark:text-neutral-400 text-xs font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Người dùng</th>
                            <th class="px-4 py-4 min-w-[200px]">Khóa học</th>
                            <th class="px-4 py-4 text-center">Số sao</th>
                            <th class="px-4 py-4 min-w-[280px]">Nội dung đánh giá</th>
                            <th class="px-4 py-4">Thời gian</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($reviews as $review)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100">{{ $review->user->name ?? 'Người dùng đã xóa' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $review->user->email ?? '' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 line-clamp-2">{{ $review->course->title ?? 'Khóa học đã xóa' }}</p>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="status-badge
                                        @if($review->rating >= 4) bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400
                                        @elseif($review->rating == 3) bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400
                                        @else bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 @endif">
                                        {{ $review->rating }}/5
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="group relative cursor-pointer"
                                         @click="
                                             detailModalOpen = true;
                                             modalType = 'review';
                                             modalUser = '{{ addslashes($review->user->name ?? 'Người dùng đã xóa') }}';
                                             modalEmail = '{{ addslashes($review->user->email ?? '') }}';
                                             modalCourse = '{{ addslashes($review->course->title ?? 'Khóa học đã xóa') }}';
                                             modalLesson = '';
                                             modalRating = {{ $review->rating ?? 5 }};
                                             modalTime = '{{ $review->created_at?->format('d/m/Y H:i') }}';
                                             modalContent = @js($review->comment ?: '— (Không có nhận xét)');
                                         ">
                                        <div class="max-h-24 overflow-y-auto pr-2 text-sm text-gray-700 dark:text-neutral-200 leading-relaxed rounded-lg group-hover:bg-[#019D91]/10 p-1.5 transition-colors">
                                            {{ $review->comment ?: '— (Không có nhận xét)' }}
                                        </div>
                                        <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-[#019D91] dark:text-[#02b3a5] opacity-75 group-hover:opacity-100">
                                            <iconify-icon icon="lucide:maximize-2" class="text-xs"></iconify-icon>
                                            Bấm để xem chi tiết
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-400 dark:text-neutral-500 whitespace-nowrap">
                                    {{ $review->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                title="Xem chi tiết"
                                                @click="
                                                    detailModalOpen = true;
                                                    modalType = 'review';
                                                    modalUser = '{{ addslashes($review->user->name ?? 'Người dùng đã xóa') }}';
                                                    modalEmail = '{{ addslashes($review->user->email ?? '') }}';
                                                    modalCourse = '{{ addslashes($review->course->title ?? 'Khóa học đã xóa') }}';
                                                    modalLesson = '';
                                                    modalRating = {{ $review->rating ?? 5 }};
                                                    modalTime = '{{ $review->created_at?->format('d/m/Y H:i') }}';
                                                    modalContent = @js($review->comment ?: '— (Không có nhận xét)');
                                                "
                                                class="p-2 text-gray-400 dark:text-neutral-500 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:bg-[#019D91]/10 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:eye" class="text-lg"></iconify-icon>
                                        </button>
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                              data-confirm-message="Bạn có chắc chắn muốn xóa đánh giá này?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Xóa vi phạm"
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
                                        <iconify-icon icon="lucide:star" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                        <p class="text-sm text-gray-500 dark:text-neutral-400">Không có đánh giá phù hợp</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reviews && $reviews->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/20">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>

    @else
        {{-- Comments Filters --}}
        <div class="glass-card p-4">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="comments">
                <div class="relative flex-1 w-full">
                    <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                           placeholder="Nội dung bình luận hoặc tên người dùng..."
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit"
                            class="flex-1 sm:flex-initial flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                        <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                        Lọc
                    </button>
                    @if(request('keyword'))
                        <a href="{{ route('admin.reviews.index', ['tab' => 'comments']) }}"
                           class="rounded-xl bg-gray-100 dark:bg-neutral-800 px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                            Đặt lại
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Comments Table --}}
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 dark:bg-neutral-800/30 text-gray-500 dark:text-neutral-400 text-xs font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Người bình luận</th>
                            <th class="px-4 py-4 min-w-[180px]">Bài học</th>
                            <th class="px-4 py-4 min-w-[200px]">Khóa học</th>
                            <th class="px-4 py-4 min-w-[300px]">Nội dung bình luận</th>
                            <th class="px-4 py-4">Thời gian</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($comments as $comment)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100">{{ $comment->user->name ?? 'Người dùng đã xóa' }}</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $comment->user->email ?? '' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 line-clamp-2">{{ $comment->lesson->title ?? 'Bài học đã xóa' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2">{{ $comment->lesson->chapter->course->title ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="group relative cursor-pointer"
                                         @click="
                                             detailModalOpen = true;
                                             modalType = 'comment';
                                             modalUser = '{{ addslashes($comment->user->name ?? 'Người dùng đã xóa') }}';
                                             modalEmail = '{{ addslashes($comment->user->email ?? '') }}';
                                             modalCourse = '{{ addslashes($comment->lesson->chapter->course->title ?? '—') }}';
                                             modalLesson = '{{ addslashes($comment->lesson->title ?? 'Bài học đã xóa') }}';
                                             modalRating = null;
                                             modalTime = '{{ $comment->created_at?->format('d/m/Y H:i') }}';
                                             modalContent = @js($comment->content);
                                         ">
                                        <div class="max-h-24 overflow-y-auto pr-2 text-sm text-gray-700 dark:text-neutral-200 leading-relaxed rounded-lg group-hover:bg-[#019D91]/10 p-1.5 transition-colors">
                                            {{ $comment->content }}
                                        </div>
                                        <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-[#019D91] dark:text-[#02b3a5] opacity-75 group-hover:opacity-100">
                                            <iconify-icon icon="lucide:maximize-2" class="text-xs"></iconify-icon>
                                            Bấm để xem chi tiết
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-400 dark:text-neutral-500 whitespace-nowrap">
                                    {{ $comment->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button"
                                                title="Xem chi tiết"
                                                @click="
                                                    detailModalOpen = true;
                                                    modalType = 'comment';
                                                    modalUser = '{{ addslashes($comment->user->name ?? 'Người dùng đã xóa') }}';
                                                    modalEmail = '{{ addslashes($comment->user->email ?? '') }}';
                                                    modalCourse = '{{ addslashes($comment->lesson->chapter->course->title ?? '—') }}';
                                                    modalLesson = '{{ addslashes($comment->lesson->title ?? 'Bài học đã xóa') }}';
                                                    modalRating = null;
                                                    modalTime = '{{ $comment->created_at?->format('d/m/Y H:i') }}';
                                                    modalContent = @js($comment->content);
                                                "
                                                class="p-2 text-gray-400 dark:text-neutral-500 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:bg-[#019D91]/10 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:eye" class="text-lg"></iconify-icon>
                                        </button>
                                        <form method="POST" action="{{ route('admin.reviews.destroy-comment', $comment) }}"
                                              data-confirm-message="Bạn có chắc chắn muốn xóa bình luận này?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Xóa vi phạm"
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
                                        <iconify-icon icon="lucide:message-square" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                        <p class="text-sm text-gray-500 dark:text-neutral-400">Không có bình luận phù hợp</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($comments && $comments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/20">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Detail Modal Pop-up --}}
    <div x-show="detailModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            {{-- Backdrop --}}
            <div x-show="detailModalOpen" @click="detailModalOpen = false"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            {{-- Modal Content --}}
            <div x-show="detailModalOpen"
                 class="relative glass-card p-6 shadow-2xl w-full max-w-xl z-10 my-8">
                {{-- Header --}}
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#019D91]/10 text-[#019D91] dark:text-[#02b3a5] flex items-center justify-center">
                            <template x-if="modalType === 'review'">
                                <iconify-icon icon="lucide:star" class="text-xl"></iconify-icon>
                            </template>
                            <template x-if="modalType === 'comment'">
                                <iconify-icon icon="lucide:message-square" class="text-xl"></iconify-icon>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-neutral-100"
                                x-text="modalType === 'review' ? 'Chi tiết Đánh giá Khóa học' : 'Chi tiết Bình luận Bài học'"></h3>
                            <p class="text-xs text-gray-400 dark:text-neutral-500" x-text="modalTime"></p>
                        </div>
                    </div>
                    <button type="button" @click="detailModalOpen = false"
                            class="p-2 text-gray-400 dark:text-neutral-500 hover:text-gray-600 dark:hover:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-lg transition-all">
                        <iconify-icon icon="lucide:x" class="text-lg"></iconify-icon>
                    </button>
                </div>

                {{-- Body Info --}}
                <div class="mt-5 space-y-4">
                    {{-- User Card --}}
                    <div class="flex items-center gap-3 p-3 bg-gray-50/80 dark:bg-neutral-800/40 rounded-xl border border-gray-100 dark:border-neutral-800">
                        <div class="w-10 h-10 rounded-full bg-[#019D91]/10 text-[#019D91] dark:text-[#02b3a5] flex items-center justify-center font-bold text-sm shrink-0"
                             x-text="modalUser ? modalUser.charAt(0).toUpperCase() : 'U'">
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate" x-text="modalUser"></p>
                            <p class="text-xs text-gray-500 dark:text-neutral-400 truncate" x-text="modalEmail"></p>
                        </div>
                    </div>

                    {{-- Course & Lesson Details --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex items-start gap-2">
                            <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider shrink-0 w-24 pt-0.5">Khóa học:</span>
                            <span class="font-medium text-gray-900 dark:text-neutral-100" x-text="modalCourse"></span>
                        </div>
                        <template x-if="modalLesson">
                            <div class="flex items-start gap-2">
                                <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider shrink-0 w-24 pt-0.5">Bài học:</span>
                                <span class="font-medium text-gray-800 dark:text-neutral-200" x-text="modalLesson"></span>
                            </div>
                        </template>
                        <template x-if="modalRating">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider shrink-0 w-24">Đánh giá:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="status-badge bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold" x-text="modalRating + '/5 sao'"></span>
                                    <div class="flex text-amber-400 text-sm">
                                        <template x-for="i in 5">
                                            <iconify-icon :icon="i <= modalRating ? 'lucide:star' : 'lucide:star'"
                                                          :class="i <= modalRating ? 'text-amber-400 fill-amber-400' : 'text-gray-300 dark:text-neutral-600'"></iconify-icon>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Full Content Box --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wider mb-1.5">
                            Nội dung chi tiết:
                        </label>
                        <div class="p-4 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200/80 dark:border-neutral-800 rounded-xl max-h-64 overflow-y-auto text-sm text-gray-800 dark:text-neutral-200 leading-relaxed whitespace-pre-line select-text"
                             x-text="modalContent"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="detailModalOpen = false"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-neutral-800 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
