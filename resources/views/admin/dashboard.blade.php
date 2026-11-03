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
                    <p class="text-sm font-medium text-gray-500">Người dùng</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['users']) }}</h3>
                </div>
                <div class="p-2.5 bg-[#019D91]/10 rounded-xl text-[#019D91]">
                    <iconify-icon icon="lucide:users-2" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.users.index') }}" class="text-xs font-medium text-[#019D91] hover:underline flex items-center gap-1">
                    Xem chi tiết
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
        </div>

        {{-- Doanh thu --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Doanh thu</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['revenue'], 0, ',', '.') }}<span class="text-xl ml-1">đ</span></h3>
                </div>
                <div class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600">
                    <iconify-icon icon="lucide:banknote" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-400">Tổng doanh thu tích lũy</span>
            </div>
        </div>

        {{-- Khóa học --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Khóa học</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['courses']) }}</h3>
                </div>
                <div class="p-2.5 bg-amber-50 rounded-xl text-amber-600">
                    <iconify-icon icon="lucide:book-open" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.courses.index') }}" class="text-xs font-medium text-[#019D91] hover:underline flex items-center gap-1">
                    Xem tất cả
                    <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                </a>
            </div>
        </div>

        {{-- Lượt ghi danh --}}
        <div class="glass-card p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500">Lượt ghi danh</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['enrollments']) }}</h3>
                </div>
                <div class="p-2.5 bg-purple-50 rounded-xl text-purple-600">
                    <iconify-icon icon="lucide:clipboard-list" class="text-2xl"></iconify-icon>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.enrollments.index') }}" class="text-xs font-medium text-[#019D91] hover:underline flex items-center gap-1">
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
                    <h2 class="text-base font-semibold text-gray-900">Xu hướng doanh thu</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Theo tháng trong năm đã chọn</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="relative inline-block">
                        <select name="year" onchange="this.form.submit()"
                                class="text-sm border border-gray-200 bg-white rounded-lg pl-3 pr-8 py-1.5 font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] cursor-pointer">
                            @forelse($availableYears as $year)
                                <option value="{{ $year }}" @selected($year === $selectedYear)>{{ $year }}</option>
                            @empty
                                <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
                            @endforelse
                        </select>
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
                    <h2 class="text-base font-semibold text-gray-900">Lượt ghi danh</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Theo tháng trong năm {{ $selectedYear }}</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="relative inline-block">
                        <select name="year" onchange="this.form.submit()"
                                class="text-sm border border-gray-200 bg-white rounded-lg pl-3 pr-8 py-1.5 font-medium text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] cursor-pointer">
                            @forelse($availableYears as $year)
                                <option value="{{ $year }}" @selected($year === $selectedYear)>{{ $year }}</option>
                            @empty
                                <option value="{{ $selectedYear }}">{{ $selectedYear }}</option>
                            @endforelse
                        </select>
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
                <h2 class="text-base font-semibold text-gray-900">Phân bố khóa học</h2>
                <p class="text-xs text-gray-400 mt-0.5">Theo trạng thái kiểm duyệt</p>
            </div>
            <div class="h-56 w-full">
                <canvas id="courseStatusChart"></canvas>
            </div>
        </div>

        {{-- Pending Courses --}}
        @if($pendingCourses->isNotEmpty())
            <div class="xl:col-span-2 glass-card overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Khóa học chờ duyệt</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Cần xem xét trước khi xuất bản</p>
                    </div>
                    <a href="{{ route('admin.courses.index', ['status' => 'pending']) }}"
                       class="text-sm font-medium text-[#019D91] hover:underline flex items-center gap-1">
                        Xem tất cả
                        <iconify-icon icon="lucide:arrow-right" class="text-sm"></iconify-icon>
                    </a>
                </div>
                <div class="divide-y divide-gray-100 max-h-72 overflow-y-auto">
                    @foreach($pendingCourses as $course)
                        <div class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                    <iconify-icon icon="lucide:book-open" class="text-amber-500 text-lg"></iconify-icon>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $course->title }}</p>
                                    <p class="text-xs text-gray-400 truncate mt-0.5">{{ $course->instructor->name ?? 'Chưa có giảng viên' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.courses.index', ['status' => 'pending', 'keyword' => $course->title]) }}"
                               class="shrink-0 text-xs font-semibold text-[#019D91] hover:underline whitespace-nowrap">
                                Xem xét
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="xl:col-span-2 glass-card p-6 flex items-center justify-center">
                <div class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-emerald-50 flex items-center justify-center mb-3">
                        <iconify-icon icon="lucide:check-circle" class="text-emerald-500 text-2xl"></iconify-icon>
                    </div>
                    <p class="text-sm font-medium text-gray-600">Không có khóa học chờ duyệt</p>
                    <p class="text-xs text-gray-400 mt-1">Tất cả khóa học đã được kiểm duyệt</p>
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

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12, weight: '500' },
                        color: '#6b7280',
                        maxRotation: 0,
                        autoSkip: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { precision: 0, font: { size: 11 }, color: '#9ca3af' }
                }
            }
        };

        new Chart(document.getElementById('revenueChart'), {
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

        new Chart(document.getElementById('enrollmentChart'), {
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

        new Chart(document.getElementById('courseStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($courseStatusChart['labels']),
                datasets: [{
                    data: @json($courseStatusChart['data']),
                    backgroundColor: ['#94a3b8', '#f59e0b', '#019D91', '#ef4444'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 11 }, padding: 12 }
                    }
                },
                cutout: '68%'
            }
        });
    });
</script>
@endpush
