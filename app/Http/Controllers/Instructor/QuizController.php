<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function create(Request $request, Course $course): View
    {
        Gate::authorize('update', $course);

        return view('instructor.quizzes.create', [
            'course' => $course,
            'quiz' => new Quiz(['chapter_id' => $request->integer('chapter_id') ?: null]),
        ]);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        Gate::authorize('update', $course);

        $quiz = $course->quizzes()->create($this->validateQuiz($request, $course));

        return redirect()->route('instructor.quizzes.show', $quiz)
            ->with('success', 'Đã tạo quiz. Hãy thêm câu hỏi trắc nghiệm.');
    }

    /**
     * Trang quản lý câu hỏi của quiz.
     */
    public function show(Quiz $quiz): View
    {
        Gate::authorize('view', $quiz->course);

        $quiz->load(['course', 'chapter', 'questions.answers'])->loadCount('attempts');

        return view('instructor.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz): View
    {
        Gate::authorize('update', $quiz->course);

        return view('instructor.quizzes.edit', [
            'course' => $quiz->course,
            'quiz' => $quiz,
        ]);
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        Gate::authorize('update', $quiz->course);

        $quiz->update($this->validateQuiz($request, $quiz->course));

        return redirect()->route('instructor.quizzes.show', $quiz)
            ->with('success', 'Đã cập nhật quiz.');
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        Gate::authorize('update', $quiz->course);

        $courseId = $quiz->course_id;
        $quiz->delete();

        return redirect()->route('instructor.courses.show', $courseId)
            ->with('success', 'Đã xóa quiz.');
    }

    /**
     * @return array{title: string, description: ?string, chapter_id: ?int}
     */
    private function validateQuiz(Request $request, Course $course): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'chapter_id' => ['nullable', 'integer', Rule::exists('chapters', 'id')->where('course_id', $course->id)],
        ], [], [
            'title' => 'tên quiz',
            'description' => 'mô tả',
            'chapter_id' => 'chương',
        ]);
    }
}
