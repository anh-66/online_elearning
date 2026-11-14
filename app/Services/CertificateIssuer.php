<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

/**
 * Cấp chứng chỉ khi học viên học xong toàn bộ bài học của khóa học.
 */
class CertificateIssuer
{
    /**
     * Số bài đã học xong / tổng số bài của khóa học.
     *
     * @return array{completed: int, total: int, percent: int}
     */
    public function progress(User $user, Course $course): array
    {
        $lessonIds = $course->lessons()->pluck('lessons.id');
        $total = $lessonIds->count();
        $completed = $total === 0 ? 0 : LessonProgress::query()
            ->where('user_id', $user->getKey())
            ->whereIn('lesson_id', $lessonIds)
            ->where('is_completed', true)
            ->count();

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? (int) round($completed * 100 / $total) : 0,
        ];
    }

    /**
     * Học viên được nhận chứng chỉ khi: có quyền học (đã ghi danh, hoặc khóa miễn phí),
     * không phải giảng viên của khóa, và đã học xong mọi bài.
     */
    public function isEligible(User $user, Course $course): bool
    {
        if ((int) $user->getKey() === (int) $course->instructor_id) {
            return false;
        }

        if (! $course->isFree() && ! $user->isEnrolledIn($course)) {
            return false;
        }

        $progress = $this->progress($user, $course);

        return $progress['total'] > 0 && $progress['completed'] >= $progress['total'];
    }

    public function find(User $user, Course $course): ?Certificate
    {
        return Certificate::query()
            ->where('user_id', $user->getKey())
            ->where('course_id', $course->getKey())
            ->first();
    }

    /**
     * Trả về chứng chỉ đã có, hoặc cấp mới nếu đủ điều kiện. Không đủ điều kiện thì trả về null.
     */
    public function issue(User $user, Course $course): ?Certificate
    {
        if ($existing = $this->find($user, $course)) {
            return $existing;
        }

        if (! $this->isEligible($user, $course)) {
            return null;
        }

        $course->loadMissing('instructor');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Certificate::create([
                    'code' => Certificate::generateCode(),
                    'user_id' => $user->getKey(),
                    'course_id' => $course->getKey(),
                    'recipient_name' => $user->name,
                    'course_title' => $course->title,
                    'instructor_name' => $course->instructor?->name,
                    'lessons_count' => $course->lessons()->count(),
                    'issued_at' => now(),
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                // Hai yêu cầu cùng lúc: chứng chỉ đã được cấp ở yêu cầu kia.
                if ($existing = $this->find($user, $course)) {
                    return $existing;
                }

                // Còn lại là trùng mã (rất hiếm), tạo mã khác rồi thử lại.
            }
        }

        throw $exception;
    }

    /**
     * Cấp bù chứng chỉ cho các khóa học viên đã học xong trước khi có tính năng này.
     */
    public function issueMissing(User $user): int
    {
        $issued = 0;

        $courses = $user->enrolledCourses()
            ->wherePivot('progress_percent', '>=', 100)
            ->whereDoesntHave('certificates', fn ($query) => $query->where('user_id', $user->getKey()))
            ->get();

        foreach ($courses as $course) {
            $issued += $this->issue($user, $course) ? 1 : 0;
        }

        return $issued;
    }
}
