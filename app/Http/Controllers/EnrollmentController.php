<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Course $course): RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return back()->with('error', 'Quản trị viên chỉ có thể xem và quản lý khóa học, không thể ghi danh.');
        }

        if ($user->id === $course->instructor_id) {
            return back()->with('error', 'Bạn không thể ghi danh khóa học của chính mình.');
        }

        if ($course->price > 0) {
            return redirect()->route('courses.checkout', $course->slug)
                ->with('error', 'Khóa học này cần hoàn tất thanh toán trước khi ghi danh.');
        }

        if ($user->isEnrolledIn($course)) {
            return redirect()->back()->with('error', 'Bạn đã đăng ký khóa học này rồi.');
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_FREE,
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('learning.player', $course->slug)
            ->with('success', 'Đăng ký khóa học thành công!');
    }
}
