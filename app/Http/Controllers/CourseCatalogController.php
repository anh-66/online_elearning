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
        }])->whereHas('courses', fn ($q) => $q->where('status', Course::STATUS_PUBLISHED))->orderBy('name')->get();

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
            ->withCount('enrollments')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Đánh giá với phân trang (eager-load học viên và giảng viên phản hồi)
        $reviews = $course->reviews()
            ->with(['user', 'instructor'])
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'review_page');

        $averageRating = $course->averageRating();
        $totalReviews = $course->reviews()->count();

        // Số lượt đánh giá theo từng mức sao (5 → 1) để vẽ thanh phân bố
        $ratingCounts = $course->reviews()
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');
        $ratingBreakdown = collect([5, 4, 3, 2, 1])
            ->mapWithKeys(fn (int $star) => [$star => (int) ($ratingCounts[$star] ?? 0)]);

        // Kiểm tra xem user đã ghi danh và đã đánh giá chưa (nếu đã đăng nhập)
        $isEnrolled = false;
        $userReview = null;
        $isWishlisted = false;
        if (auth()->check()) {
            $isEnrolled = $course->enrollments()
                ->where('user_id', auth()->id())
                ->exists();

            $userReview = $course->reviews()
                ->where('user_id', auth()->id())
                ->first();
            $isWishlisted = $course->wishlists()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('courses.show', compact(
            'course',
            'reviews',
            'averageRating',
            'totalReviews',
            'ratingBreakdown',
            'isEnrolled',
            'userReview',
            'isWishlisted'
        ));
    }
}
