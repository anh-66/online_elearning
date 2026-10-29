@extends('layouts.instructor')

@section('title', 'Studio Giảng viên - Tổng quan')
@section('page_title', 'Bảng điều khiển Giảng viên')

@section('content')
<div class="space-y-6">
    <!-- Thẻ thống kê -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tổng số khóa học</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_courses'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['published_courses'] }} đã xuất bản · {{ $stats['pending_courses'] }} chờ duyệt</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Học viên</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_students'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Không trùng lặp giữa các khóa</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lượt ghi danh</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_enrollments'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Trên tất cả khóa học</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Đánh giá trung bình</p>
                <p class="text-2xl font-bold text-amber-500 mt-1">{{ $stats['total_reviews'] > 0 ? number_format($stats['average_rating'], 1) : '—' }} ★</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['total_reviews'] }} lượt đánh giá</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Biểu đồ ghi danh -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-900 mb-4">Lượt ghi danh 6 tháng gần nhất</h3>
            <div class="h-72 w-full">
                <canvas id="instructorEnrollmentChart"></canvas>
            </div>
        </div>

        <!-- Ghi danh gần đây -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Ghi danh gần đây</h3>
                <a href="{{ route('instructor.students.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Xem tất cả</a>
            </div>
            <ul class="space-y-3">
                @forelse($recentEnrollments as $enrollment)
                    <li class="flex items-start space-x-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center shrink-0">
                            {{ mb_strtoupper(mb_substr($enrollment->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $enrollment->course->title }}</p>
                            <p class="text-[11px] text-gray-400">{{ $enrollment->enrolled_at?->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">Chưa có học viên nào ghi danh.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Thống kê theo khóa học -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900">Thống kê theo khóa học</h3>
                <a href="{{ route('instructor.courses.create') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">+ Tạo khóa học</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Khóa học</th>
                            <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                            <th class="px-4 py-3 text-center font-semibold">Học viên</th>
                            <th class="px-4 py-3 text-center font-semibold">Đánh giá</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <a href="{{ route('instructor.courses.show', $course) }}" class="font-semibold text-gray-900 hover:text-indigo-600">{{ $course->title }}</a>
                                </td>
                                <td class="px-4 py-3">@include('instructor.partials.status-badge', ['status' => $course->status])</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('instructor.students.index', ['course_id' => $course->id]) }}" class="font-semibold text-indigo-600 hover:underline">{{ $course->enrollments_count }}</a>
                                </td>
                                <td class="px-4 py-3 text-center text-amber-500 font-semibold">
                                    {{ $course->reviews_count > 0 ? number_format($course->reviews_avg_rating, 1).' ★' : '—' }}
                                    <span class="text-xs text-gray-400 font-normal">({{ $course->reviews_count }})</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                    Bạn chưa có khóa học nào. <a href="{{ route('instructor.courses.create') }}" class="text-indigo-600 font-semibold hover:underline">Tạo khóa học đầu tiên</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Đánh giá mới -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-base font-bold text-gray-900 mb-4">Đánh giá mới nhất</h3>
            <ul class="space-y-4">
                @forelse($recentReviews as $review)
                    <li class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $review->user->name }}</p>
                            <span class="text-amber-500 text-xs font-bold">{{ str_repeat('★', $review->rating) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                        </div>
                        <p class="text-xs text-gray-500 truncate">{{ $review->course->title }}</p>
                        @if($review->comment)
                            <p class="text-sm text-gray-700 mt-1 line-clamp-2">{{ $review->comment }}</p>
                        @endif
                    </li>
                @empty
                    <li class="text-sm text-gray-500">Chưa có đánh giá nào.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('instructorEnrollmentChart');
        if (!canvas || !window.Chart) {
            return;
        }

        new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json($monthlyEnrollments['labels']),
                datasets: [{
                    label: 'Lượt ghi danh',
                    data: @json($monthlyEnrollments['data']),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    });
</script>
@endpush
