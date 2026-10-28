<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;

class InstructorProfileController extends Controller
{
    /**
     * Hiển thị trang profile công khai của giảng viên.
     */
    public function show(int $id)
    {
        $instructor = User::where('role', User::ROLE_INSTRUCTOR)
            ->findOrFail($id);

        // Danh sách khóa học đã published của giảng viên
        $courses = Course::with(['category', 'reviews'])
            ->where('instructor_id', $instructor->id)
            ->published()
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('created_at')
            ->paginate(9);

        $totalStudents = Course::where('instructor_id', $instructor->id)
            ->published()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');

        $averageRating = Course::where('instructor_id', $instructor->id)
            ->published()
            ->withAvg('reviews', 'rating')
            ->get()
            ->avg('reviews_avg_rating');

        return view('instructors.show', compact('instructor', 'courses', 'totalStudents', 'averageRating'));
    }
}
