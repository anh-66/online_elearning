<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    private function checkEnrollment($course_id) {
        $enrolled = Enrollment::where('user_id', Auth::id())->where('course_id', $course_id)->exists();
        if (!$enrolled) abort(403, 'Bạn chưa đăng ký khóa học này.');
    }

    public function show(Course $course)
    {
        $this->checkEnrollment($course->id);
        
        // Lấy bài học đầu tiên thông qua quan hệ hasManyThrough
        $firstLesson = $course->lessons()->orderBy('order')->first();
        if (!$firstLesson) return redirect()->route('my-courses')->with('error', 'Khóa học chưa có bài học.');

        return redirect()->route('learning.lesson', [$course->id, $firstLesson->id]);
    }

    public function lesson(Course $course, Lesson $lesson)
    {
        $this->checkEnrollment($course->id);
        
        $completedLessonIds = LessonProgress::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->pluck('lesson_id')->toArray();

        $prevLesson = $course->lessons()->where('order', '<', $lesson->order)->orderBy('order', 'desc')->first();
        $nextLesson = $course->lessons()->where('order', '>', $lesson->order)->orderBy('order', 'asc')->first();

        return view('learning.player', compact('course', 'lesson', 'completedLessonIds', 'prevLesson', 'nextLesson'));
    }

    public function markAsComplete(Lesson $lesson)
    {
        $user = Auth::user();
        
        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['is_completed' => true]
        );

        // Lấy course thông qua lesson -> chapter -> course
        $course = $lesson->chapter->course; 
        $totalLessons = $course->lessons()->count();
        $completedCount = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)
            ->update(['progress_percent' => $percent]);

        return response()->json([
            'success' => true,
            'progress_percent' => $percent,
        ]);
    }
}
