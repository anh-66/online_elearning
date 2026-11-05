<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Course $course)
    {
        $user = Auth::user();
        
        $exists = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Bạn đã đăng ký khóa học này rồi.');
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'progress_percent' => 0
        ]);

        return redirect()->route('learning.show', $course->id)->with('success', 'Đăng ký khóa học thành công!');
    }
}