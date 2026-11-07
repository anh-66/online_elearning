<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Phòng học trực tuyến (Learning Player) - TV3 tích hợp với Module Quiz & Hỏi đáp của TV4.
 */
class LearningController extends Controller
{
    /**
     * Giao diện phòng học: video, nội dung, điều hướng bài học, hỏi đáp và quiz cuối chương.
     */
    public function player(Request $request, string $course_slug, ?int $lesson_id = null): View|RedirectResponse
    {
        $course = Course::with([
            'chapters' => fn ($q) => $q->orderBy('order'),
            'chapters.lessons' => fn ($q) => $q->orderBy('order'),
            'chapters.quizzes',
        ])
            ->where('slug', $course_slug)
            ->firstOrFail();

        // Kiểm tra quyền truy cập: Học viên phải ghi danh (hoặc là giảng viên của khóa/admin)
        $user = $request->user();
        $isInstructorOrAdmin = $user->isAdmin() || $course->instructor_id === $user->id;

        if (! $isInstructorOrAdmin && ! $user->isEnrolledIn($course)) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Bạn cần ghi danh khóa học để vào phòng học.');
        }

        // Lấy danh sách tất cả các bài học theo thứ tự chương và bài
        $allLessons = $course->chapters->flatMap->lessons->values();

        if ($allLessons->isEmpty()) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Khóa học này hiện chưa có bài học nào.');
        }

        // Xác định bài học hiện tại
        $currentLesson = $lesson_id
            ? $allLessons->firstWhere('id', $lesson_id)
            : $allLessons->first();

        if (! $currentLesson) {
            $currentLesson = $allLessons->first();
        }

        // Lấy danh sách bài học đã hoàn thành của học viên
        $completedLessonIds = $user->lessonProgress()
            ->whereIn('lesson_id', $allLessons->pluck('id'))
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->all();

        // Tính tiến độ phần trăm
        $totalLessons = $allLessons->count();
        $progressPercent = $totalLessons > 0
            ? (int) round((count($completedLessonIds) / $totalLessons) * 100)
            : 0;

        // Tìm bài học trước và bài học sau
        $currentIndex = $allLessons->search(fn ($item) => $item->id === $currentLesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex < $totalLessons - 1 ? $allLessons->get($currentIndex + 1) : null;

        return view('learning.player', compact(
            'course',
            'currentLesson',
            'completedLessonIds',
            'progressPercent',
            'prevLesson',
            'nextLesson'
        ));
    }

    /**
     * Đánh dấu hoặc hủy đánh dấu hoàn thành bài học.
     */
    public function markComplete(Request $request, int $id): RedirectResponse
    {
        $lesson = Lesson::with('chapter.course')->findOrFail($id);
        $course = $lesson->chapter->course;
        $user = $request->user();

        $isInstructorOrAdmin = $user->isAdmin() || $course->instructor_id === $user->id;
        if (! $isInstructorOrAdmin && ! $user->isEnrolledIn($course)) {
            abort(403);
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $progress->is_completed = ! (bool) $progress->is_completed;
        $progress->completed_at = $progress->is_completed ? now() : null;
        $progress->save();

        $statusMsg = $progress->is_completed ? 'Đã hoàn thành bài học!' : 'Đã bỏ đánh dấu hoàn thành bài học.';

        return back()->with('success', $statusMsg);
    }
}
