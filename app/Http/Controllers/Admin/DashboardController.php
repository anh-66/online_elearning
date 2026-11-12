<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Số tháng hiển thị trên biểu đồ lượt ghi danh.
     */
    private const CHART_MONTHS = 12;

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $stats = [
            'users' => User::count(),
            'courses' => Course::count(),
            'enrollments' => Enrollment::count(),
            'pending_courses' => Course::pending()->count(),
            'reviews' => Review::count(),
            'revenue' => $this->revenue(),
        ];

        $availableYears = $this->enrollmentYears();
        $selectedYear = (int) ($validated['year'] ?? ($availableYears->first() ?? now()->year));
        if (! $availableYears->contains($selectedYear)) {
            $selectedYear = $availableYears->first() ?? now()->year;
        }

        $enrollmentChart = $this->enrollmentsByMonth($selectedYear);
        $revenueChart = $this->revenueByMonth($selectedYear);

        $courseStatusChart = [
            'labels' => ['Bản nháp', 'Chờ duyệt', 'Đã xuất bản', 'Bị từ chối'],
            'data' => [
                Course::where('status', Course::STATUS_DRAFT)->count(),
                Course::where('status', Course::STATUS_PENDING)->count(),
                Course::where('status', Course::STATUS_PUBLISHED)->count(),
                Course::where('status', Course::STATUS_REJECTED)->count(),
            ],
        ];

        $pendingCourses = Course::with(['instructor', 'category'])
            ->pending()
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'enrollmentChart',
            'revenueChart',
            'courseStatusChart',
            'pendingCourses',
            'availableYears',
            'selectedYear'
        ));
    }

    /**
     * Lấy các năm có lượt ghi danh trong cơ sở dữ liệu.
     *
     * @return Collection<int, int>
     */
    private function enrollmentYears(): Collection
    {
        $yearExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y', enrolled_at)"
            : 'YEAR(enrolled_at)';

        return Enrollment::query()
            ->whereNotNull('enrolled_at')
            ->selectRaw("{$yearExpression} as year")
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(static fn ($year): int => (int) $year);
    }

    /**
     * Đếm lượt ghi danh theo từng tháng của năm được chọn.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function enrollmentsByMonth(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = $start->copy()->endOfYear();

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', enrolled_at)"
            : "DATE_FORMAT(enrolled_at, '%Y-%m')";

        $counts = Enrollment::query()
            ->whereBetween('enrolled_at', [$start, $end])
            ->selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = 0; $i < self::CHART_MONTHS; $i++) {
            $month = Carbon::parse($start)->addMonths($i);
            $labels[] = (string) $month->format('n');
            $data[] = (int) ($counts[$month->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function revenue(): float
    {
        return (float) Enrollment::query()
            ->where('enrollments.status', Enrollment::STATUS_PAID)
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->sum('courses.price');
    }

    /**
     * Tính doanh thu theo từng tháng của năm được chọn từ các lượt ghi danh đã thanh toán.
     *
     * @return array{labels: array<int, string>, data: array<int, float>}
     */
    private function revenueByMonth(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end = $start->copy()->endOfYear();

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', enrollments.enrolled_at)"
            : "DATE_FORMAT(enrollments.enrolled_at, '%Y-%m')";

        $revenues = Enrollment::query()
            ->where('enrollments.status', Enrollment::STATUS_PAID)
            ->whereBetween('enrollments.enrolled_at', [$start, $end])
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->selectRaw("{$monthExpression} as month, SUM(courses.price) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = 0; $i < self::CHART_MONTHS; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = (string) $month->format('n');
            $data[] = (float) ($revenues[$month->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
