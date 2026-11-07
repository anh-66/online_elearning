<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\User;

/**
 * Quyền hỏi đáp dưới bài học: học viên đã ghi danh và giảng viên của khóa được bình luận,
 * mỗi người chỉ sửa/xóa bình luận của chính mình.
 */
class LessonCommentPolicy
{
    public function create(User $user, Lesson $lesson): bool
    {
        $course = $lesson->chapter->course;

        return $course->instructor_id === $user->id || $user->isEnrolledIn($course);
    }

    public function update(User $user, LessonComment $lessonComment): bool
    {
        return $lessonComment->user_id === $user->id;
    }

    public function delete(User $user, LessonComment $lessonComment): bool
    {
        return $lessonComment->user_id === $user->id;
    }
}
