<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCourseController extends Controller
{
    public function index()
    {
        // Lấy danh sách khóa học user đã ghi danh (tùy cấu trúc model của bạn)
        // Tạm thời trả về thông báo hoặc view để test luồng
        // Hoặc nếu bạn đã có file giao diện: return view('my-courses');
        return view('dashboard');
    }
}