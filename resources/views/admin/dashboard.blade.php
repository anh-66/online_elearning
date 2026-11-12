@extends('layouts.admin')

@section('title', 'Quản trị hệ thống - Dashboard')
@section('page_title', 'Tổng quan hệ thống')

@section('content')
<div class="space-y-6">

    {{-- KPI Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Người dùng --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Người dùng</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-1">{{ number_format($stats['users']) }}</h3>
                </div>
                <div class="p-2.5 bg-[#019D91]/10 rounded-xl text-[#019D91] dark:text-[#02b3a5]">
                    <iconify-icon icon="lucide:users-2" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1">
                    Xem chi tiết
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
        </div>

        {{-- Doanh thu --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Doanh thu</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-1">{{ number_format($stats['revenue'], 0, ',', '.') }}<span class="text-xl ml-1">đ</span></h3>
                </div>
                <div class="p-2.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <iconify-icon icon="lucide:banknote" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-400 dark:text-neutral-500">Tổng doanh thu tích lũy</span>
            </div>
        </div>

        {{-- Khóa học --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Khóa học</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-1">{{ number_format($stats['courses']) }}</h3>
                </div>
                <div class="p-2.5 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-600 dark:text-amber-400">
                    <iconify-icon icon="lucide:book-open" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.courses.index') }}" class="text-xs font-medium text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1">
                    Xem tất cả
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
        </div>

        {{-- Lượt ghi danh --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-neutral-400">Lượt ghi danh</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-1">{{ number_format($stats['enrollments']) }}</h3>
                </div>
                <div class="p-2.5 bg-purple-50 dark:bg-purple-500/10 rounded-xl text-purple-600 dark:text-purple-400">
                    <iconify-icon icon="lucide:clipboard-list" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.enrollments.index') }}" class="text-xs font-medium text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1">
                    Xem chi tiết
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Revenue Chart --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Xu hướng doanh thu</h2>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Theo tháng trong năm đã chọn</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="relative inline-block"
                         x-data="{
                             open: false,
                             selectedValue: '{{ $selectedYear }}',
                             selectedLabel: 'Năm {{ $selectedYear }}',
                             options: [
                                 @forelse($availableYears as $year)
                                     { value: '{{ $year }}', label: 'Năm {{ $year }}' },
                                 @empty
                                     { value: '{{ $selectedYear }}', label: 'Năm {{ $selectedYear }}' },
                                 @endforelse
                             ],
                             select(val, label) {
                                 if (this.selectedValue === val) { this.open = false; return; }
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                                 this.$nextTick(() => { this.$el.closest('form').submit(); });
                             }
                         }">
                        <input type="hidden" name="year" :value="selectedValue">
                        <button type="button" @click="open = !open"
                                class="text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-800 py-1.5 pl-3 pr-2.5 bg-white dark:bg-neutral-900 hover:bg-gray-50 dark:hover:bg-neutral-800 flex items-center gap-2 focus:border-[#019D91] focus:ring-1 focus:ring-[#019D91]/20 cursor-pointer shadow-xs">
                            <span x-text="selectedLabel" class="font-semibold text-gray-800 dark:text-neutral-200"></span>
                            <iconify-icon icon="lucide:chevron-down" class="text-xs text-gray-400 dark:text-neutral-500 transition-transform" :class="open ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                             class="absolute right-0 mt-1 z-50 rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl p-1 min-w-[120px] text-xs space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button" @click="select(item.value, item.label)"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/40 text-left transition-colors cursor-pointer"
                                        :class="selectedValue === item.value ? 'bg-neutral-100 dark:bg-neutral-800 font-semibold text-neutral-900 dark:text-white' : 'text-neutral-700 dark:text-neutral-300'">
                                    <span x-text="item.label"></span>
                                    <iconify-icon icon="lucide:check" x-show="selectedValue === item.value" class="text-xs text-neutral-900 dark:text-white"></iconify-icon>
                                </button>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
            <div class="h-64 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Enrollment Chart --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Lượt ghi danh</h2>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Theo tháng trong năm {{ $selectedYear }}</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="relative inline-block"
                         x-data="{
                             open: false,
                             selectedValue: '{{ $selectedYear }}',
                             selectedLabel: 'Năm {{ $selectedYear }}',
                             options: [
                                 @forelse($availableYears as $year)
                                     { value: '{{ $year }}', label: 'Năm {{ $year }}' },
                                 @empty
                                     { value: '{{ $selectedYear }}', label: 'Năm {{ $selectedYear }}' },
                                 @endforelse
                             ],
                             select(val, label) {
                                 if (this.selectedValue === val) { this.open = false; return; }
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                                 this.$nextTick(() => { this.$el.closest('form').submit(); });
                             }
                         }">
                        <input type="hidden" name="year" :value="selectedValue">
                        <button type="button" @click="open = !open"
                                class="text-xs sm:text-sm rounded-xl border border-gray-200 dark:border-neutral-800 py-1.5 pl-3 pr-2.5 bg-white dark:bg-neutral-900 hover:bg-gray-50 dark:hover:bg-neutral-800 flex items-center gap-2 focus:border-[#019D91] focus:ring-1 focus:ring-[#019D91]/20 cursor-pointer shadow-xs">
                            <span x-text="selectedLabel" class="font-semibold text-gray-800 dark:text-neutral-200"></span>
                            <iconify-icon icon="lucide:chevron-down" class="text-xs text-gray-400 dark:text-neutral-500 transition-transform" :class="open ? 'rotate-180' : ''"></iconify-icon>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                             class="absolute right-0 mt-1 z-50 rounded-xl border border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xl p-1 min-w-[120px] text-xs space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button" @click="select(item.value, item.label)"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-700/40 text-left transition-colors cursor-pointer"
                                        :class="selectedValue === item.value ? 'bg-neutral-100 dark:bg-neutral-800 font-semibold text-neutral-900 dark:text-white' : 'text-neutral-700 dark:text-neutral-300'">
                                    <span x-text="item.label"></span>
                                    <iconify-icon icon="lucide:check" x-show="selectedValue === item.value" class="text-xs text-neutral-900 dark:text-white"></iconify-icon>
                                </button>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
            <div class="h-64 w-full">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Course Status + Pending Courses --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Course Status Doughnut --}}
        <div class="glass-card p-6">
            <div class="mb-6">
                <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Phân bố khóa học</h2>
                <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Theo trạng thái kiểm duyệt</p>
            </div>
            <div class="h-56 w-full">
                <canvas id="courseStatusChart"></canvas>
            </div>
        </div>

        {{-- Pending Courses --}}
        @if($pendingCourses->isNotEmpty())
            <div class="xl:col-span-2 glass-card overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-neutral-800 px-6 py-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Khóa học chờ duyệt</h2>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-0.5">Cần xem xét trước khi xuất bản</p>
                    </div>
                    <a href="{{ route('admin.courses.index', ['status' => 'pending']) }}"
                       class="text-sm font-medium text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1">
                        Xem tất cả
                        <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-neutral-800 max-h-72 overflow-y-auto">
                    @foreach($pendingCourses as $course)
                        <div class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center shrink-0">
                                    <iconify-icon icon="lucide:book-open" class="text-amber-500 dark:text-amber-400 text-lg"></iconify-icon>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate">{{ $course->title }}</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500 truncate mt-0.5">{{ $course->instructor->name ?? 'Chưa có giảng viên' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.courses.index', ['status' => 'pending', 'keyword' => $course->title]) }}"
                               class="shrink-0 text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline whitespace-nowrap">
                                Xem xét
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="xl:col-span-2 glass-card p-6 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center mb-3">
                        <iconify-icon icon="lucide:check-circle" class="text-emerald-500 dark:text-emerald-400 text-2xl"></iconify-icon>
                    </div>
                    <p class="text-sm font-medium text-gray-600 dark:text-neutral-300">Không có khóa học chờ duyệt</p>
                    <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Tất cả khóa học đã được kiểm duyệt</p>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Chart) {
            return;
        }

        const chartPalette = () => {
            const isDark = document.documentElement.classList.contains('dark');

            return {
                label: isDark ? '#a3a3a3' : '#6b7280',
                tick: isDark ? '#737373' : '#9ca3af',
                grid: isDark ? '#262626' : '#f3f4f6',
                surface: isDark ? '#171717' : '#ffffff',
            };
        };
        const palette = chartPalette();
        Chart.defaults.color = palette.label;

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12, weight: '500' },
                        color: palette.label,
                        maxRotation: 0,
                        autoSkip: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: palette.grid },
                    ticks: { precision: 0, font: { size: 11 }, color: palette.tick }
                }
            }
        };

        const revenueChart = new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: 'Doanh thu',
                    data: @json($revenueChart['data']),
                    borderColor: '#019D91',
                    backgroundColor: 'rgba(1, 157, 145, 0.08)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#019D91',
                    borderWidth: 2,
                }]
            },
            options: {
                ...baseOptions,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function(items) {
                                return 'Tháng ' + items[0].label;
                            },
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' đ';
                            }
                        }
                    }
                },
                scales: {
                    ...baseOptions.scales,
                    y: {
                        ...baseOptions.scales.y,
                        suggestedMin: 0,
                        suggestedMax: 2000000,
                        ticks: {
                            ...baseOptions.scales.y.ticks,
                            stepSize: 500000,
                            callback: function(value) {
                                if (value === 0) return '0';
                                if (value >= 1000000) {
                                    const tr = value / 1000000;
                                    return (Number.isInteger(tr) ? tr : tr.toFixed(1)) + ' tr';
                                }
                                if (value >= 1000) {
                                    return (value / 1000) + 'k';
                                }
                                return value;
                            }
                        }
                    }
                }
            }
        });

        const enrollmentChart = new Chart(document.getElementById('enrollmentChart'), {
            type: 'bar',
            data: {
                labels: @json($enrollmentChart['labels']),
                datasets: [{
                    label: 'Lượt ghi danh',
                    data: @json($enrollmentChart['data']),
                    backgroundColor: 'rgba(1, 157, 145, 0.15)',
                    borderColor: '#019D91',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                ...baseOptions,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function(items) {
                                return 'Tháng ' + items[0].label;
                            },
                            label: function(context) {
                                return 'Lượt ghi danh: ' + context.raw;
                            }
                        }
                    }
                }
            }
        });

        const courseStatusChart = new Chart(document.getElementById('courseStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($courseStatusChart['labels']),
                datasets: [{
                    data: @json($courseStatusChart['data']),
                    backgroundColor: ['#94a3b8', '#f59e0b', '#019D91', '#ef4444'],
                    borderWidth: 3,
                    borderColor: palette.surface
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 11 }, padding: 12, color: palette.label }
                    }
                },
                cutout: '68%'
            }
        });

        // Đổi màu trục/lưới khi người dùng bấm nút sáng/tối mà không cần tải lại trang
        window.addEventListener('theme-changed', () => {
            const next = chartPalette();
            Chart.defaults.color = next.label;

            [revenueChart, enrollmentChart].forEach((chart) => {
                chart.options.scales.x.ticks.color = next.label;
                chart.options.scales.y.ticks.color = next.tick;
                chart.options.scales.y.grid.color = next.grid;
                chart.update('none');
            });

            courseStatusChart.data.datasets[0].borderColor = next.surface;
            courseStatusChart.options.plugins.legend.labels.color = next.label;
            courseStatusChart.update('none');
        });
    });
</script>
@endpush
