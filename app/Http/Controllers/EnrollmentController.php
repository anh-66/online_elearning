<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function enroll($id)
    {
        $userId = Auth::id();

        // Kiểm tra xem đã ghi danh khóa này chưa trong bảng enrollments
        $exists = DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('course_id', $id)
            ->exists();

        if (!$exists) {
            DB::table('enrollments')->insert([
                'user_id' => $userId,
                'course_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Ghi danh thành công!');
    }
}