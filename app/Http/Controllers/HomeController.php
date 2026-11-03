<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với banner, danh mục nổi bật, khóa học nổi bật và thống kê.
     */
    public function index()
    {
        // Thông báo hệ thống mới nhất (tối đa 3 thông báo)
        $latestNotifications = Notification::latest()->limit(3)->get();

        // Banner đang hoạt động, sắp xếp theo order
        $banners = Banner::where('is_active', true)
            ->orderBy('order')
            ->get();

        // Danh mục nổi bật (lấy tối đa 8 danh mục có nhiều khóa học published nhất)
        $featuredCategories = Category::withCount(['courses' => function ($query) {
            $query->where('status', Course::STATUS_PUBLISHED);
        }])
            ->whereHas('courses', fn ($query) => $query->where('status', Course::STATUS_PUBLISHED))
            ->orderByDesc('courses_count')
            ->limit(8)
            ->get();

        // Khóa học nổi bật: published, sắp xếp theo số lượt ghi danh và đánh giá
        $featuredCourses = Course::with(['instructor', 'category', 'reviews'])
            ->published()
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(8)
            ->get();

        // 4 thẻ số liệu tổng quan
        $stats = [
            'total_courses' => Course::published()->count(),
            'total_students' => User::where('role', User::ROLE_STUDENT)->count(),
            'total_instructors' => User::where('role', User::ROLE_INSTRUCTOR)->count(),
            'total_enrollments' => Enrollment::count(),
        ];

        return view('home', compact('banners', 'featuredCategories', 'featuredCourses', 'stats', 'latestNotifications'));
    }
}
