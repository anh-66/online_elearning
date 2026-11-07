<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Danh sách học viên đã ghi danh các khóa của giảng viên, kèm tiến độ học của từng người.
     */
    public function index(Request $request): View
    {
        $courses = $request->user()->courses()->orderBy('title')->get(['id', 'title']);
        $courseIds = $courses->pluck('id');

        $enrollments = Enrollment::query()
            ->with(['user', 'course.chapters.lessons', 'course.quizzes'])
            ->whereIn('course_id', $courseIds)
            ->when($request->filled('course_id'), fn ($query) => $query->where('course_id', $request->integer('course_id')))
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;
                $query->whereHas('user', fn ($userQuery) => $userQuery->where(fn ($nameOrEmail) => $nameOrEmail
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")));
            })
            ->latest('enrolled_at')
            ->paginate(15)
            ->withQueryString();

        $collection = $enrollments->getCollection();
        $progress = $this->calculateProgress($collection);
        $studentDetails = $this->buildStudentDetails($collection, $progress);

        $selectedCourse = $request->filled('course_id')
            ? $courses->firstWhere('id', $request->integer('course_id'))
            : null;

        return view('instructor.students.index', compact('courses', 'enrollments', 'progress', 'studentDetails', 'selectedCourse'));
    }

    /**
     * Chuẩn bị dữ liệu chi tiết tiến độ các bài học và quiz cho từng học viên để hiển thị modal.
     */
    private function buildStudentDetails($enrollments, array $progress): array
    {
        if ($enrollments->isEmpty()) {
            return [];
        }

        $userIds = $enrollments->pluck('user_id')->unique();

        $completedProgress = LessonProgress::query()
            ->where('is_completed', true)
            ->whereIn('user_id', $userIds)
            ->get(['user_id', 'lesson_id', 'completed_at'])
            ->groupBy('user_id');

        $quizAttempts = QuizAttempt::query()
            ->whereIn('user_id', $userIds)
            ->latest()
            ->get(['user_id', 'quiz_id', 'score', 'is_passed', 'created_at'])
            ->groupBy('user_id');

        return $enrollments->mapWithKeys(function (Enrollment $enrollment) use ($completedProgress, $quizAttempts, $progress) {
            $userCompleted = $completedProgress->get($enrollment->user_id, collect())->keyBy('lesson_id');
            $userQuizzes = $quizAttempts->get($enrollment->user_id, collect())->groupBy('quiz_id');
            $prog = $progress[$enrollment->id] ?? ['completed' => 0, 'total' => 0, 'percent' => 0];

            $chapters = $enrollment->course->chapters->map(function ($chapter) use ($userCompleted) {
                return [
                    'title' => $chapter->title,
                    'lessons' => $chapter->lessons->map(function ($lesson) use ($userCompleted) {
                        $p = $userCompleted->get($lesson->id);

                        return [
                            'title' => $lesson->title,
                            'is_completed' => (bool) $p,
                            'completed_at' => $p?->completed_at ? $p->completed_at->format('d/m/Y H:i') : null,
                        ];
                    })->all(),
                ];
            })->all();

            $quizzes = $enrollment->course->quizzes->map(function ($quiz) use ($userQuizzes) {
                $attempts = $userQuizzes->get($quiz->id, collect());
                $best = $attempts->sortByDesc('score')->first();

                return [
                    'title' => $quiz->title,
                    'has_attempted' => (bool) $best,
                    'score' => $best?->score,
                    'is_passed' => (bool) $best?->is_passed,
                ];
            })->all();

            return [$enrollment->id => [
                'enrollment_id' => $enrollment->id,
                'student_name' => $enrollment->user->name,
                'student_email' => $enrollment->user->email,
                'course_title' => $enrollment->course->title,
                'enrolled_at' => $enrollment->enrolled_at?->format('d/m/Y H:i'),
                'completed' => $prog['completed'],
                'total' => $prog['total'],
                'percent' => $prog['percent'],
                'chapters' => $chapters,
                'quizzes' => $quizzes,
            ]];
        })->all();
    }

    /**
     * Tính % hoàn thành cho từng lượt ghi danh: số bài đã hoàn thành / tổng số bài của khóa.
     *
     * @param  iterable<Enrollment>  $enrollments
     * @return array<int, array{completed: int, total: int, percent: int}>
     */
    private function calculateProgress(iterable $enrollments): array
    {
        $enrollments = collect($enrollments);

        if ($enrollments->isEmpty()) {
            return [];
        }

        $courseIds = $enrollments->pluck('course_id')->unique();

        $lessonTotals = Course::whereIn('id', $courseIds)
            ->withCount('lessons')
            ->pluck('lessons_count', 'id');

        $completedCounts = LessonProgress::query()
            ->join('lessons', 'lessons.id', '=', 'lesson_progress.lesson_id')
            ->join('chapters', 'chapters.id', '=', 'lessons.chapter_id')
            ->where('lesson_progress.is_completed', true)
            ->whereIn('lesson_progress.user_id', $enrollments->pluck('user_id')->unique())
            ->whereIn('chapters.course_id', $courseIds)
            ->selectRaw('lesson_progress.user_id, chapters.course_id, count(*) as completed')
            ->groupBy('lesson_progress.user_id', 'chapters.course_id')
            ->get()
            ->keyBy(fn ($row) => $row->user_id.'-'.$row->course_id);

        return $enrollments->mapWithKeys(function (Enrollment $enrollment) use ($lessonTotals, $completedCounts) {
            $total = (int) ($lessonTotals[$enrollment->course_id] ?? 0);
            $completed = (int) ($completedCounts[$enrollment->user_id.'-'.$enrollment->course_id]->completed ?? 0);

            return [$enrollment->id => [
                'completed' => $completed,
                'total' => $total,
                'percent' => $total > 0 ? (int) round(min($completed, $total) / $total * 100) : 0,
            ]];
        })->all();
    }
}
