<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\QuestionRequest;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function store(QuestionRequest $request, Quiz $quiz): RedirectResponse
    {
        Gate::authorize('update', $quiz->course);

        DB::transaction(function () use ($request, $quiz) {
            $question = $quiz->questions()->create($request->safe()->only(['question_text', 'explanation']));
            $this->syncAnswers($question, $request->validated('answers'), (int) $request->validated('correct_answer'));
        });

        return redirect()->route('instructor.quizzes.show', $quiz)
            ->with('success', 'Đã thêm câu hỏi.');
    }

    public function edit(Question $question): View
    {
        $quiz = $question->quiz;
        Gate::authorize('update', $quiz->course);

        $question->load('answers');

        return view('instructor.questions.edit', compact('question', 'quiz'));
    }

    public function update(QuestionRequest $request, Question $question): RedirectResponse
    {
        Gate::authorize('update', $question->quiz->course);

        DB::transaction(function () use ($request, $question) {
            $question->update($request->safe()->only(['question_text', 'explanation']));
            $this->syncAnswers($question, $request->validated('answers'), (int) $request->validated('correct_answer'));
        });

        return redirect()->route('instructor.quizzes.show', $question->quiz_id)
            ->with('success', 'Đã cập nhật câu hỏi.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        Gate::authorize('update', $question->quiz->course);

        $question->delete();

        return back()->with('success', 'Đã xóa câu hỏi.');
    }

    /**
     * Ghi lại toàn bộ đáp án của câu hỏi (chỉ một đáp án đúng).
     *
     * @param  array<int, string>  $answers
     */
    private function syncAnswers(Question $question, array $answers, int $correctIndex): void
    {
        $question->answers()->delete();

        foreach ($answers as $index => $answerText) {
            $question->answers()->create([
                'answer_text' => $answerText,
                'is_correct' => $index === $correctIndex,
            ]);
        }
    }
}
