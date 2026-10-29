<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

/**
 * Quyền thao tác của giảng viên trên khóa học trong Studio (TV5).
 * Admin được xem/sửa mọi khóa học, giảng viên chỉ thao tác khóa của chính mình.
 */
class CoursePolicy
{
    public function view(User $user, Course $course): bool
    {
        return $user->isAdmin() || $course->instructor_id === $user->id;
    }

    /**
     * Khóa đang chờ duyệt (pending) bị khóa chỉnh sửa để admin duyệt đúng nội dung đã gửi.
     */
    public function update(User $user, Course $course): bool
    {
        return $this->view($user, $course) && ! $course->isPending();
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }

    public function submit(User $user, Course $course): bool
    {
        return $this->view($user, $course) && ($course->isDraft() || $course->isRejected());
    }

    public function withdraw(User $user, Course $course): bool
    {
        return $this->view($user, $course) && $course->isPending();
    }
}
