<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = $request->user()
            ->enrolledCourses()
            ->with(['instructor', 'category', 'reviews'])
            ->withCount('enrollments')
            ->latest('enrollments.enrolled_at')
            ->paginate(12)
            ->withQueryString();

        $latestNotifications = Notification::query()
            ->latest()
            ->limit(3)
            ->get();

        return view('my-courses.index', compact('courses', 'latestNotifications'));
    }
}
