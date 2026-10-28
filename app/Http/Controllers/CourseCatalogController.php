<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseCatalogController extends Controller
{
    /**
     * Danh sách khóa học công khai với tìm kiếm, lọc và phân trang.
     */
    public function index(Request $request)
    {
        $query = Course::with(['instructor', 'category', 'reviews'])
            ->published()
            ->withCount('enrollments');

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Lọc theo giá
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('price', 0);
            } elseif ($request->price === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        // Lọc theo trình độ
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Sắp xếp
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'popular' => $query->orderByDesc('enrollments_count'),
            default => $query->orderByDesc('created_at'),
        };

        $courses = $query->paginate(12)->withQueryString();
        $categories = Category::withCount(['courses' => function ($q) {
            $q->where('status', Course::STATUS_PUBLISHED);
        }])->having('courses_count', '>', 0)->orderBy('name')->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    /**
     * Trang chi tiết khóa học.
     */
    public function show(string $slug)
    {
        $course = Course::with([
            'instructor',
            'category',
            'images',
            'chapters.lessons',
            'chapters.quizzes',
        ])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Đánh giá với phân trang
        $reviews = $course->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'review_page');

        $averageRating = $course->averageRating();
        $totalReviews = $course->reviews()->count();

        // Kiểm tra xem user đã ghi danh chưa (nếu đã đăng nhập)
        $isEnrolled = false;
        if (auth()->check()) {
            $isEnrolled = $course->enrollments()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('courses.show', compact('course', 'reviews', 'averageRating', 'totalReviews', 'isEnrolled'));
    }
}
