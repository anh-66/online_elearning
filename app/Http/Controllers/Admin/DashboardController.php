<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $ownCoursesOnly = fn (Builder $query) => $query->when(! $user->isAdmin(), fn (Builder $query) => $query->where('instructor_id', $user->id));

        $courseQuery = Course::query()->tap($ownCoursesOnly);
        $enrollmentQuery = Enrollment::query()->whereHas('course', $ownCoursesOnly);

        $months = collect(range(5, 0))->map(fn (int $monthsAgo) => now()->startOfMonth()->subMonths($monthsAgo));
        $recentEnrollments = (clone $enrollmentQuery)
            ->where('created_at', '>=', $months->first())
            ->get(['created_at'])
            ->countBy(fn (Enrollment $enrollment) => $enrollment->created_at->format('m/Y'));

        return view('admin.dashboard', [
            'stats' => [
                'courses' => (clone $courseQuery)->count(),
                'students' => User::where('role', UserRole::Student)->count(),
                'enrollments' => (clone $enrollmentQuery)->count(),
                'reviews' => Review::whereHas('course', $ownCoursesOnly)->count(),
            ],
            'enrollmentChart' => [
                'labels' => $months->map(fn ($month) => 'T'.$month->format('m/Y'))->all(),
                'values' => $months->map(fn ($month) => $recentEnrollments->get($month->format('m/Y'), 0))->all(),
            ],
            'categoryChart' => Category::withCount('courses')->orderByDesc('courses_count')->take(6)->get(['id', 'name']),
            'latestEnrollments' => (clone $enrollmentQuery)->with(['user', 'course'])->latest()->take(6)->get(),
            'popularCourses' => (clone $courseQuery)->withCount('enrollments')->orderByDesc('enrollments_count')->take(5)->get(),
        ]);
    }
}
