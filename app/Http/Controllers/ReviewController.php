<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $id)
    {
        // 1. Kiểm tra dữ liệu người dùng gửi lên (bắt buộc phải có số sao từ 1-5)
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 2. Lưu đánh giá vào bảng reviews trong Database
        DB::table('reviews')->insert([
            'user_id' => Auth::id(),
            'course_id' => $id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Quay lại trang hiện tại kèm thông báo thành công
        return back()->with('success', 'Cảm ơn bạn đã đánh giá khóa học!');
    }
}