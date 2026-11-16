@extends('layouts.instructor')

@section('title', 'Studio Giảng viên - Tổng quan')
@section('page_title', 'Bảng điều khiển Giảng viên')

@section('content')
<div class="min-w-0 space-y-6">

    {{-- 1. Header: Lời chào mừng & Tác vụ nhanh --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-neutral-100 tracking-tight">
                Chào mừng trở lại, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-neutral-400 mt-1">
                Theo dõi hiệu suất giảng dạy, lượt ghi danh và doanh thu tích lũy của bạn.
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('instructor.courses.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#019D91] hover:bg-[#008b80] text-white text-xs sm:text-sm font-semibold shadow-xs transition active:scale-95">
                <iconify-icon icon="lucide:plus" class="text-base"></iconify-icon>
                <span>Tạo khóa học mới</span>
            </a>
            <a href="{{ route('instructor.payouts.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-800 text-gray-700 dark:text-neutral-300 text-xs sm:text-sm font-semibold shadow-xs transition active:scale-95">
                <iconify-icon icon="lucide:wallet" class="text-base"></iconify-icon>
                <span>Rút tiền / Ví</span>
            </a>
        </div>
    </div>

    {{-- 2. Thẻ Thống Kê KPI (5 thẻ tinh gọn, phong cách tối giản phẳng) --}}
    <div class="grid min-w-0 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">

        {{-- Thẻ 1: Ví Giảng Viên --}}
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between transition hover:border-[#019D91]/40">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Ví Giảng Viên</span>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">
                        {{ number_format(auth()->user()->instructorAvailableBalance(), 0, ',', '.') }}<span class="text-base font-semibold text-gray-400 ml-0.5">₫</span>
                    </h3>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <iconify-icon icon="lucide:wallet" class="text-xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-400">
                <span class="truncate">Tích lũy 70%</span>
                <a href="{{ route('instructor.payouts.index') }}" class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline shrink-0">
                    Chi tiết →
                </a>
            </div>
        </div>

        {{-- Thẻ 2: Khóa Học --}}
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between transition hover:border-[#019D91]/40">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Khóa Học</span>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">
                        {{ $stats['total_courses'] }}
                    </h3>
                </div>
                <div class="p-2.5 rounded-xl bg-[#019D91]/10 text-[#019D91] dark:text-[#02b3a5]">
                    <iconify-icon icon="lucide:book-open" class="text-xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-400">
                <span class="truncate">{{ $stats['published_courses'] }} xuất bản · {{ $stats['pending_courses'] }} duyệt</span>
                <a href="{{ route('instructor.courses.index') }}" class="font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline shrink-0">
                    Xem tất cả →
                </a>
            </div>
        </div>

        {{-- Thẻ 3: Học Viên --}}
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between transition hover:border-[#019D91]/40">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Học Viên</span>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">
                        {{ number_format($stats['total_students']) }}
                    </h3>
                </div>
                <div class="p-2.5 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400">
                    <iconify-icon icon="lucide:users" class="text-xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-400">
                <span class="truncate">Không trùng lặp</span>
                <a href="{{ route('instructor.students.index') }}" class="font-semibold text-sky-600 dark:text-sky-400 hover:underline shrink-0">
                    Danh sách →
                </a>
            </div>
        </div>

        {{-- Thẻ 4: Lượt Ghi Danh --}}
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between transition hover:border-[#019D91]/40">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Lượt Ghi Danh</span>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">
                        {{ number_format($stats['total_enrollments']) }}
                    </h3>
                </div>
                <div class="p-2.5 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
                    <iconify-icon icon="lucide:clipboard-list" class="text-xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-400">
                <span class="truncate">Toàn bộ khóa học</span>
                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">Tích lũy</span>
            </div>
        </div>

        {{-- Thẻ 5: Đánh Giá Trung Bình --}}
        <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between transition hover:border-[#019D91]/40">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Đánh Giá</span>
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight flex items-center gap-1.5">
                        {{ $stats['total_reviews'] > 0 ? number_format($stats['average_rating'], 1) : '—' }}
                        <span class="text-amber-500 text-lg">★</span>
                    </h3>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <iconify-icon icon="lucide:star" class="text-xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-400">
                <span class="truncate">{{ $stats['total_reviews'] }} lượt đánh giá</span>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Học viên</span>
            </div>
        </div>

    </div>

    {{-- 3. Phần giữa: Biểu đồ ghi danh & Danh sách học viên mới --}}
    <div class="grid min-w-0 grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Biểu đồ ghi danh 6 tháng gần nhất --}}
        <div class="min-w-0 lg:col-span-2 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">
                        Xu hướng ghi danh 6 tháng gần nhất
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">
                        Số lượng học viên mới đăng ký các khóa học theo từng tháng
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-neutral-400">
                    Tổng: {{ number_format($stats['total_enrollments']) }} lượt
                </span>
            </div>
            <div class="h-72 w-full">
                <canvas id="instructorEnrollmentChart"></canvas>
            </div>
        </div>

        {{-- Học viên mới ghi danh --}}
        <div class="min-w-0 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-neutral-800">
                    <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">
                        Ghi danh gần đây
                    </h2>
                    <a href="{{ route('instructor.students.index') }}"
                       class="text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline">
                        Xem tất cả →
                    </a>
                </div>

                <ul class="space-y-3.5">
                    @forelse($recentEnrollments as $enrollment)
                        <li class="flex items-center space-x-3 group">
                            <div class="w-9 h-9 rounded-full bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 font-bold text-xs flex items-center justify-center shrink-0 overflow-hidden shadow-xs">
                                @if($enrollment->user->avatar_url)
                                    <img src="{{ $enrollment->user->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $enrollment->user->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                    <span class="hidden flex items-center justify-center w-full h-full">{{ $enrollment->user->initialLetter() }}</span>
                                @else
                                    <span>{{ $enrollment->user->initialLetter() }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate group-hover:text-[#019D91] transition-colors">
                                    {{ $enrollment->user->name }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500 truncate">
                                    {{ $enrollment->course->title }}
                                </p>
                            </div>
                            <span class="text-[11px] font-medium text-gray-400 dark:text-neutral-500 shrink-0">
                                {{ $enrollment->enrolled_at?->locale('vi')->diffForHumans(null, true) }}
                            </span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 dark:text-neutral-500 py-6 text-center">
                            Chưa có học viên nào ghi danh gần đây.
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800 text-center">
                <a href="{{ route('instructor.students.index') }}" class="text-xs font-medium text-gray-500 dark:text-neutral-400 hover:text-[#019D91] transition">
                    Quản lý toàn bộ học viên của bạn →
                </a>
            </div>
        </div>

    </div>

    {{-- 4. Phần dưới: Bảng thống kê khóa học & Đánh giá gần đây --}}
    <div class="grid min-w-0 grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Bảng thống kê khóa học --}}
        <div class="min-w-0 lg:col-span-2 bg-white dark:bg-neutral-900 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">
                        Hiệu suất khóa học của bạn
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">
                        Tình trạng xuất bản và mức độ tương tác của học viên
                    </p>
                </div>
                <a href="{{ route('instructor.courses.create') }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#019D91] text-white text-xs font-semibold hover:bg-[#008b80] transition">
                    <iconify-icon icon="lucide:plus" class="text-sm"></iconify-icon>
                    <span>Tạo mới</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50/60 dark:bg-neutral-800/40 text-xs uppercase text-gray-400 dark:text-neutral-500 border-b border-gray-100 dark:border-neutral-800">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold">Tên khóa học</th>
                            <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Trạng thái</th>
                            <th class="px-4 py-3 text-center font-semibold whitespace-nowrap">Học viên</th>
                            <th class="px-4 py-3 text-center font-semibold whitespace-nowrap">Đánh giá</th>
                            <th class="px-6 py-3 text-right font-semibold whitespace-nowrap">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-neutral-800/50 transition">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('instructor.courses.show', $course) }}"
                                       class="font-semibold text-gray-900 dark:text-neutral-100 hover:text-[#019D91] dark:hover:text-[#02b3a5] transition line-clamp-1">
                                        {{ $course->title }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @include('instructor.partials.status-badge', ['status' => $course->status])
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <a href="{{ route('instructor.students.index', ['course_id' => $course->id]) }}"
                                       class="font-semibold text-gray-800 dark:text-neutral-200 hover:text-[#019D91] hover:underline">
                                        {{ number_format($course->enrollments_count) }}
                                    </a>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($course->reviews_count > 0)
                                        <span class="font-semibold text-amber-500">
                                            {{ number_format($course->reviews_avg_rating, 1) }} ★
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-neutral-500 font-normal">({{ $course->reviews_count }})</span>
                                    @else
                                        <span class="text-gray-400 text-xs">Chưa có</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('instructor.courses.show', $course) }}"
                                       class="text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline">
                                        Chi tiết →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 dark:text-neutral-500">
                                    Bạn chưa có khóa học nào. <a href="{{ route('instructor.courses.create') }}" class="text-[#019D91] dark:text-[#02b3a5] font-semibold hover:underline">Tạo khóa học đầu tiên</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Đánh giá mới nhất của học viên --}}
        <div class="min-w-0 bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-xs flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100 mb-4 pb-2 border-b border-gray-100 dark:border-neutral-800">
                    Đánh giá mới nhất
                </h2>

                <ul class="space-y-4">
                    @forelse($recentReviews as $review)
                        <li class="border-b border-gray-50 dark:border-neutral-800/60 pb-3.5 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate">
                                    {{ $review->user->name }}
                                </p>
                                <span class="text-amber-500 text-xs font-bold tracking-wider">
                                    {{ str_repeat('★', $review->rating) }}<span class="text-gray-200 dark:text-neutral-700">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-neutral-500 truncate">
                                {{ $review->course->title }}
                            </p>
                            @if($review->comment)
                                <p class="text-xs text-gray-600 dark:text-neutral-300 mt-1.5 line-clamp-2 bg-gray-50 dark:bg-neutral-800/40 p-2 rounded-lg leading-relaxed">
                                    “{{ $review->comment }}”
                                </p>
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 dark:text-neutral-500 py-6 text-center">
                            Chưa có đánh giá nào từ học viên.
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-neutral-800 text-center">
                <span class="text-xs text-gray-400 dark:text-neutral-500">
                    Phản hồi chân thật giúp cải thiện bài giảng
                </span>
            </div>
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

        const chartPalette = () => {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                label: isDark ? '#a3a3a3' : '#6b7280',
                tick: isDark ? '#737373' : '#9ca3af',
                grid: isDark ? '#262626' : '#f3f4f6',
            };
        };
        const palette = chartPalette();

        new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json($monthlyEnrollments['labels']),
                datasets: [{
                    label: 'Lượt ghi danh',
                    data: @json($monthlyEnrollments['data']),
                    backgroundColor: '#019D91',
                    hoverBackgroundColor: '#008b80',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#171717',
                        titleColor: '#ffffff',
                        bodyColor: '#e5e5e5',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: (context) => `${context.parsed.y} học viên ghi danh`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 12, weight: '500' },
                            color: palette.label,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: palette.grid },
                        ticks: {
                            precision: 0,
                            font: { family: 'Inter', size: 11 },
                            color: palette.tick,
                            stepSize: 1
                        }
                    }
                },
            },
        });
    });
</script>
@endpush
