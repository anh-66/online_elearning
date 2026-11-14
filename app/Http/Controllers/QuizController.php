<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitQuizRequest;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\AchievementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Làm bài quiz trắc nghiệm, chấm điểm tự động và xem lịch sử làm bài (TV4).
 */
class QuizController extends Controller
{
    /**
     * Giao diện làm bài quiz.
     */
    public function show(Request $request, Quiz $quiz): View
    {
        Gate::authorize('take', $quiz);

        $quiz->load(['course', 'chapter', 'questions.answers']);

        $recentAttempts = $quiz->attempts()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->take(5)
            ->get();

        return view('quizzes.do-quiz', compact('quiz', 'recentAttempts'));
    }

    /**
     * Nhận bài làm, chấm điểm và lưu vào lịch sử quiz_attempts.
     */
    public function submit(SubmitQuizRequest $request, Quiz $quiz): RedirectResponse
    {
        Gate::authorize('take', $quiz);

        $questions = $quiz->questions()->with('answers')->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'Quiz này chưa có câu hỏi nào.');
        }

        $submittedAnswers = (array) $request->validated('answers', []);
        $selectedAnswers = [];
        $totalCorrect = 0;

        foreach ($questions as $question) {
            $answer = $question->answers->firstWhere('id', (int) ($submittedAnswers[$question->id] ?? 0));

            if ($answer === null) {
                continue;
            }

            $selectedAnswers[$question->id] = $answer->id;

            if ($answer->is_correct) {
                $totalCorrect++;
            }
        }

        $score = (int) round($totalCorrect / $questions->count() * 100);

        $attemptData = [
            'user_id' => $request->user()->id,
            'score' => $score,
        ];

        // Kiểm tra an toàn các cột có trong bảng trước khi chèn (tương thích cả khi chưa chạy migration mới)
        if (Schema::hasColumn('quiz_attempts', 'total_correct')) {
            $attemptData['total_correct'] = $totalCorrect;
        }

        if (Schema::hasColumn('quiz_attempts', 'total_questions')) {
            $attemptData['total_questions'] = $questions->count();
        }

        if (Schema::hasColumn('quiz_attempts', 'is_passed')) {
            $attemptData['is_passed'] = $score >= QuizAttempt::PASSING_SCORE;
        }

        if (Schema::hasColumn('quiz_attempts', 'selected_answers')) {
            $attemptData['selected_answers'] = $selectedAnswers;
        }

        $attempt = $quiz->attempts()->create($attemptData);

        app(AchievementService::class)->syncUserAchievements(auth()->user());

        return redirect()->route('quiz.result', [$quiz, $attempt])
            ->with('success', "Nộp bài thành công! Bạn trả lời đúng {$totalCorrect}/{$questions->count()} câu.");
    }

    /**
     * Kết quả chi tiết của một lần làm bài.
     */
    public function result(Quiz $quiz, QuizAttempt $attempt): View
    {
        Gate::authorize('viewAttempt', [$quiz, $attempt]);

        $quiz->load(['course', 'chapter', 'questions.answers']);

        return view('quizzes.result', compact('quiz', 'attempt'));
    }

    /**
     * Lịch sử các lần làm bài của học viên với quiz này.
     */
    public function history(Request $request, Quiz $quiz): View
    {
        Gate::authorize('take', $quiz);

        $quiz->load(['course', 'chapter']);

        $userAttemptsQuery = $quiz->attempts()->where('user_id', $request->user()->id);

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            if ($request->status === 'passed') {
                $userAttemptsQuery->where('is_passed', true);
            } elseif ($request->status === 'failed') {
                $userAttemptsQuery->where('is_passed', false);
            }
        }

        $attempts = (clone $userAttemptsQuery)->latest()->paginate(10)->withQueryString();
        $bestScore = (clone $quiz->attempts()->where('user_id', $request->user()->id))->max('score');
        $avgScore = (clone $quiz->attempts()->where('user_id', $request->user()->id))->avg('score') ?? 0;
        $totalAttempts = (clone $quiz->attempts()->where('user_id', $request->user()->id))->count();

        return view('quizzes.history', compact('quiz', 'attempts', 'bestScore', 'avgScore', 'totalAttempts'));
    }
}
