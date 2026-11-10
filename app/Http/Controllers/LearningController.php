<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LearningController extends Controller
{
    private function canAccess(Course $course): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->id === $course->instructor_id) {
            return true;
        }

        if ($course->isFree()) {
            return true;
        }

        return $user->isEnrolledIn($course);
    }

    public function player(Course $course, ?int $lesson_id = null): View|RedirectResponse
    {
        if (! $this->canAccess($course)) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Bạn chưa đăng ký khóa học này.');
        }

        $course->load(['chapters.lessons', 'chapters.quizzes']);
        $currentLesson = $lesson_id
            ? $course->lessons()->whereKey($lesson_id)->firstOrFail()
            : $course->lessons()->orderBy('order')->first();

        if (! $currentLesson) {
            return redirect()->route('my-courses')->with('error', 'Khóa học chưa có bài học.');
        }

        $completedLessonIds = LessonProgress::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->pluck('lesson_id')->toArray();

        $lessons = $course->lessons->sortBy('order')->values();
        $currentIndex = $lessons->search(fn (Lesson $lesson): bool => $lesson->id === $currentLesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;
        $enrollment = Enrollment::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        $progressPercent = (int) ($enrollment?->progress_percent ?? 0);

        return view('learning.player', compact(
            'course',
            'currentLesson',
            'completedLessonIds',
            'prevLesson',
            'nextLesson',
            'progressPercent'
        ));
    }

    public function markComplete(Request $request, Lesson $lesson): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $course = $lesson->chapter()->with('course')->firstOrFail()->course;
        abort_unless($this->canAccess($course), 403, 'Bạn chưa đăng ký khóa học này.');

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $lessonIds = $course->lessons()->pluck('lessons.id');
        $totalLessons = $lessonIds->count();
        $completedCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->where('is_completed', true)
            ->count();

        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->update(['progress_percent' => $percent]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'progress_percent' => $percent]);
        }

        return back()->with('success', 'Đã đánh dấu hoàn thành bài học.');
    }
}
