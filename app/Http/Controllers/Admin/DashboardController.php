<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Số tháng hiển thị trên biểu đồ lượt ghi danh.
     */
    private const CHART_MONTHS = 12;

    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'courses' => Course::count(),
            'enrollments' => Enrollment::count(),
            'pending_courses' => Course::pending()->count(),
            'reviews' => Review::count(),
        ];

        $enrollmentChart = $this->enrollmentsByMonth();

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

        return view('admin.dashboard', compact('stats', 'enrollmentChart', 'courseStatusChart', 'pendingCourses'));
    }

    /**
     * Đếm lượt ghi danh theo tháng trong 12 tháng gần nhất (tháng không có dữ liệu = 0).
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function enrollmentsByMonth(): array
    {
        $start = now()->startOfMonth()->subMonths(self::CHART_MONTHS - 1);

        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', enrolled_at)"
            : "DATE_FORMAT(enrolled_at, '%Y-%m')";

        $counts = Enrollment::query()
            ->where('enrolled_at', '>=', $start)
            ->selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = 0; $i < self::CHART_MONTHS; $i++) {
            $month = Carbon::parse($start)->addMonths($i);
            $labels[] = 'Tháng '.$month->format('m/Y');
            $data[] = (int) ($counts[$month->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
