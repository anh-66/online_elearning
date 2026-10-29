<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Thống kê tổng quan của giảng viên: khóa học, học viên, đánh giá trung bình, lượt ghi danh theo tháng.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()->courses()
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->get();

        $courseIds = $courses->pluck('id');
        $enrollmentQuery = Enrollment::whereIn('course_id', $courseIds);
        $reviewQuery = Review::whereIn('course_id', $courseIds);

        $stats = [
            'total_courses' => $courses->count(),
            'published_courses' => $courses->where('status', Course::STATUS_PUBLISHED)->count(),
            'pending_courses' => $courses->where('status', Course::STATUS_PENDING)->count(),
            'total_enrollments' => (clone $enrollmentQuery)->count(),
            'total_students' => (clone $enrollmentQuery)->distinct()->count('user_id'),
            'average_rating' => round((float) ((clone $reviewQuery)->avg('rating') ?? 0), 1),
            'total_reviews' => (clone $reviewQuery)->count(),
        ];

        $recentEnrollments = (clone $enrollmentQuery)
            ->with(['user', 'course'])
            ->latest('enrolled_at')
            ->take(6)
            ->get();

        $recentReviews = (clone $reviewQuery)
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $monthlyEnrollments = $this->monthlyEnrollments(clone $enrollmentQuery);

        return view('instructor.dashboard', compact('courses', 'stats', 'recentEnrollments', 'recentReviews', 'monthlyEnrollments'));
    }

    /**
     * Số lượt ghi danh 6 tháng gần nhất (nhóm bằng PHP để chạy được trên cả MySQL lẫn SQLite).
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function monthlyEnrollments(Builder $enrollmentQuery): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(5);

        $counts = $enrollmentQuery
            ->where('enrolled_at', '>=', $start)
            ->pluck('enrolled_at')
            ->countBy(fn ($date) => Carbon::parse($date)->format('Y-m'));

        $labels = [];
        $data = [];

        for ($month = $start->copy(); $month->lte(Carbon::now()); $month->addMonth()) {
            $labels[] = $month->format('m/Y');
            $data[] = (int) ($counts[$month->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
