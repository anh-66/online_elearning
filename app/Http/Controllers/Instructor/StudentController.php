<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
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
            ->with(['user', 'course'])
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

        $progress = $this->calculateProgress($enrollments->getCollection());

        $selectedCourse = $request->filled('course_id')
            ? $courses->firstWhere('id', $request->integer('course_id'))
            : null;

        return view('instructor.students.index', compact('courses', 'enrollments', 'progress', 'selectedCourse'));
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
