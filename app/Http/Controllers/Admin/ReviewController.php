<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonComment;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Kiểm duyệt nội dung: tab "Đánh giá" và tab "Bình luận bài học".
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tab' => ['nullable', Rule::in(['reviews', 'comments'])],
            'keyword' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);

        $tab = $validated['tab'] ?? 'reviews';
        $keyword = $validated['keyword'] ?? null;

        $reviews = null;
        $comments = null;

        if ($tab === 'reviews') {
            $reviews = Review::query()
                ->with(['user', 'course'])
                ->when($keyword, function ($query, string $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('comment', 'like', "%{$keyword}%")
                            ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
                            ->orWhereHas('course', fn ($query) => $query->where('title', 'like', "%{$keyword}%"));
                    });
                })
                ->when($validated['rating'] ?? null, fn ($query, int $rating) => $query->where('rating', $rating))
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } else {
            $comments = LessonComment::query()
                ->with(['user', 'lesson.chapter.course'])
                ->when($keyword, function ($query, string $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('content', 'like', "%{$keyword}%")
                            ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$keyword}%"));
                    });
                })
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return view('admin.reviews.index', compact('tab', 'reviews', 'comments'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá vi phạm.');
    }

    public function destroyComment(LessonComment $comment): RedirectResponse
    {
        // Các phản hồi con bị xóa theo nhờ cascade ở bảng lesson_comments.
        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận vi phạm.');
    }
}
