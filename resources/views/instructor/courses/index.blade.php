@extends('layouts.instructor')

@section('title', 'Khóa học của tôi')
@section('page_title', 'Khóa học của tôi')

@section('content')
@php
    $statusTabs = [
        '' => 'Tất cả',
        'draft' => 'Bản nháp',
        'pending' => 'Chờ duyệt',
        'published' => 'Đã xuất bản',
        'rejected' => 'Bị từ chối',
    ];
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
@endphp

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Tab trạng thái -->
        <div class="flex flex-wrap gap-2">
            @foreach($statusTabs as $value => $label)
                <a href="{{ route('instructor.courses.index', array_filter(['status' => $value, 'keyword' => request('keyword')])) }}"
                   class="px-3.5 py-1.5 rounded-full text-sm font-medium border transition {{ request('status', '') === $value ? 'bg-brand-700 text-white border-brand-700' : 'bg-white dark:bg-neutral-900 text-gray-600 dark:text-neutral-300 border-gray-200 dark:border-neutral-800 hover:border-brand-300 dark:hover:border-brand-500/50' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs opacity-75">{{ $value === '' ? $statusCounts->sum() : ($statusCounts[$value] ?? 0) }}</span>
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('instructor.courses.index') }}" class="flex">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tên khóa học..."
                       class="w-56 rounded-l-xl border-gray-300 dark:border-neutral-700 text-sm focus:border-brand-600 focus:ring-brand-600">
                <button type="submit" class="px-4 rounded-r-xl bg-brand-700 text-white text-sm font-medium hover:bg-brand-800">Tìm</button>
            </form>
            <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-brand-700 text-white text-sm font-semibold shadow-sm hover:bg-brand-800 whitespace-nowrap">
                + Tạo khóa học
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-neutral-800/40 text-xs uppercase text-gray-500 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Khóa học</th>
                        <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                        <th class="px-4 py-3 text-center font-semibold">Nội dung</th>
                        <th class="px-4 py-3 text-center font-semibold">Học viên</th>
                        <th class="px-4 py-3 text-center font-semibold">Đánh giá</th>
                        <th class="px-4 py-3 text-right font-semibold">Học phí</th>
                        <th class="px-6 py-3 text-right font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse($courses as $course)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 align-top">
                            <td class="px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-20 h-12 rounded-lg bg-gray-100 dark:bg-neutral-800 overflow-hidden shrink-0">
                                        @if($course->thumbnail)
                                            <img src="{{ Storage::disk('public')->url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 dark:text-neutral-500 text-xs">No image</div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('instructor.courses.show', $course) }}" class="font-semibold text-gray-900 dark:text-neutral-100 hover:text-brand-700 dark:hover:text-brand-300 line-clamp-2">{{ $course->title }}</a>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $course->category->name ?? 'Chưa phân loại' }} · {{ $levelLabels[$course->level] ?? $course->level }}</p>
                                        @if($course->isRejected() && $course->reject_reason)
                                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">Lý do từ chối: {{ $course->reject_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">@include('instructor.partials.status-badge', ['status' => $course->status])</td>
                            <td class="px-4 py-4 text-center text-gray-600 dark:text-neutral-300 whitespace-nowrap">{{ $course->chapters_count }} chương · {{ $course->lessons_count }} bài</td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('instructor.students.index', ['course_id' => $course->id]) }}" class="font-semibold text-brand-700 dark:text-brand-400 hover:underline">{{ $course->enrollments_count }}</a>
                            </td>
                            <td class="px-4 py-4 text-center text-amber-500 font-semibold whitespace-nowrap">
                                {{ $course->reviews_avg_rating ? number_format($course->reviews_avg_rating, 1).' ★' : '—' }}
                            </td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">
                                {{ (float) $course->price > 0 ? number_format($course->price, 0, ',', '.').'đ' : 'Miễn phí' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    <a href="{{ route('instructor.courses.show', $course) }}" class="px-3 py-1.5 rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-800 dark:text-brand-300 text-xs font-semibold hover:bg-brand-100 dark:hover:bg-brand-500/15">Nội dung</a>
                                    @can('update', $course)
                                        <a href="{{ route('instructor.courses.edit', $course) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs font-semibold hover:bg-gray-200 dark:hover:bg-neutral-800">Sửa</a>
                                    @endcan
                                    @can('delete', $course)
                                        <form method="POST" action="{{ route('instructor.courses.destroy', $course) }}" onsubmit="return confirm('Xóa khóa học này cùng toàn bộ chương, bài học và quiz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-xs font-semibold hover:bg-rose-100 dark:hover:bg-rose-500/15">Xóa</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-neutral-400">
                                Không tìm thấy khóa học nào.
                                <a href="{{ route('instructor.courses.create') }}" class="text-brand-700 dark:text-brand-400 font-semibold hover:underline">Tạo khóa học mới</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $courses->links() }}</div>
</div>
@endsection
