<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        $enrolledCourses = $user->enrolledCourses()
            ->with(['instructor', 'category', 'reviews'])
            ->withCount('enrollments')
            ->latest('enrollments.enrolled_at')
            ->get();

        $wishlistCourses = $user->wishlistCourses()
            ->with(['instructor', 'category', 'reviews'])
            ->withCount('enrollments')
            ->latest('wishlists.created_at')
            ->get();

        $enrollmentTotal = $user->enrollments()->count();
        $completedTotal = $user->enrollments()->where('progress_percent', 100)->count();
        $averageProgress = (int) round($user->enrollments()->avg('progress_percent') ?? 0);

        return view('dashboard', compact(
            'enrolledCourses',
            'wishlistCourses',
            'enrollmentTotal',
            'completedTotal',
            'averageProgress'
        ));
    }
}
