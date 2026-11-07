<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $courses = $user->enrolledCourses()
            ->with(['instructor', 'category', 'reviews'])
            ->withCount('enrollments')
            ->orderByDesc('enrollments.enrolled_at')
            ->paginate(9, ['courses.*']);

        $wishlistCourses = $user->wishlistCourses()
            ->with(['instructor', 'category', 'reviews'])
            ->withCount('enrollments')
            ->latest('wishlists.created_at')
            ->get();

        $enrollmentTotal = $user->enrollments()->count();
        $completedTotal = $user->enrollments()->where('progress_percent', 100)->count();
        $averageProgress = (int) round($user->enrollments()->avg('progress_percent') ?? 0);

        return view('my-courses.index', compact(
            'courses',
            'wishlistCourses',
            'enrollmentTotal',
            'completedTotal',
            'averageProgress'
        ));
    }
}
