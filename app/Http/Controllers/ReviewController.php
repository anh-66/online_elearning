<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Đánh giá sao khóa học: mỗi học viên đã ghi danh chỉ đánh giá một lần/khóa (TV4).
 * Giảng viên của khóa học hoặc Admin có quyền phản hồi đánh giá của học viên.
 * Điểm trung bình được tính trực tiếp từ bảng reviews qua Course::averageRating().
 */
class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Course $course): RedirectResponse
    {
        if ($request->user()->reviews()->where('course_id', $course->id)->exists()) {
            return $this->backToReviews($course)
                ->with('error', 'Bạn đã đánh giá khóa học này rồi. Bạn có thể chỉnh sửa đánh giá cũ.');
        }

        Gate::authorize('create', [Review::class, $course]);

        $course->reviews()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return $this->backToReviews($course)->with('success', 'Cảm ơn bạn đã đánh giá khóa học!');
    }

    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $review->update($request->validated());

        return $this->backToReviews($review->course)->with('success', 'Đã cập nhật đánh giá của bạn.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $course = $review->course;
        $review->delete();

        return $this->backToReviews($course)->with('success', 'Đã xóa đánh giá của bạn.');
    }

    /**
     * Giảng viên hoặc Admin phản hồi đánh giá của học viên.
     */
    public function reply(Request $request, Review $review): RedirectResponse
    {
        Gate::authorize('reply', $review);

        $validated = $request->validate([
            'instructor_reply' => ['required', 'string', 'max:2000'],
        ], [
            'instructor_reply.required' => 'Vui lòng nhập nội dung phản hồi.',
            'instructor_reply.max' => 'Phản hồi không được vượt quá 2000 ký tự.',
        ]);

        $review->update([
            'instructor_reply' => $validated['instructor_reply'],
            'instructor_id' => $request->user()->id,
            'replied_at' => now(),
        ]);

        return $this->backToReviews($review->course)->with('success', 'Đã gửi phản hồi đánh giá của bạn.');
    }

    /**
     * Xóa phản hồi đánh giá của giảng viên.
     */
    public function destroyReply(Review $review): RedirectResponse
    {
        Gate::authorize('reply', $review);

        $review->update([
            'instructor_reply' => null,
            'instructor_id' => null,
            'replied_at' => null,
        ]);

        return $this->backToReviews($review->course)->with('success', 'Đã xóa phản hồi đánh giá.');
    }

    private function backToReviews(Course $course): RedirectResponse
    {
        return redirect()->to(route('courses.show', $course->slug).'#reviews');
    }
}
