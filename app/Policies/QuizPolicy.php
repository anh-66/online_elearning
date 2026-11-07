<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;

/**
 * Quyền làm bài và xem kết quả quiz của học viên (TV4).
 */
class QuizPolicy
{
    /**
     * Chỉ học viên đã ghi danh khóa học mới được làm quiz.
     */
    public function take(User $user, Quiz $quiz): bool
    {
        return $user->isEnrolledIn($quiz->course);
    }

    /**
     * Học viên chỉ xem được kết quả bài làm của chính mình.
     */
    public function viewAttempt(User $user, Quiz $quiz, QuizAttempt $attempt): bool
    {
        return $attempt->user_id === $user->id && $attempt->quiz_id === $quiz->id;
    }
}
