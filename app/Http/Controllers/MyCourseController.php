<?php

namespace App\Http\Controllers;

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

        return view('my-courses.index', compact('courses'));
    }
}
