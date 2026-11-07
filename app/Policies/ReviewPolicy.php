<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;

/**
 * Quyền đánh giá khóa học: mỗi học viên đã ghi danh chỉ đánh giá một lần cho mỗi khóa học.
 * Giảng viên của khóa học hoặc Admin có quyền phản hồi đánh giá của học viên.
 */
class ReviewPolicy
{
    public function create(User $user, Course $course): bool
    {
        return $user->isEnrolledIn($course)
            && ! $user->reviews()->where('course_id', $course->id)->exists();
    }

    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $review->user_id === $user->id || $user->isAdmin();
    }

    public function reply(User $user, Review $review): bool
    {
        return $user->isAdmin() || $review->course->instructor_id === $user->id;
    }
}
