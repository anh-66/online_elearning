<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Lưu bình luận mới
    public function store(Request $request, $lessonId)
    {
        // 1. Kiểm tra nội dung bình luận (không được để trống)
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        // 2. Thêm vào Database (Bảng comments)
        DB::table('comments')->insert([
            'user_id' => Auth::id(),
            'lesson_id' => $lessonId, 
            'content' => $request->content,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Tải lại trang và báo thành công
        return back()->with('success', 'Bình luận của bạn đã được gửi!');
    }
}