<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// Nhớ import các Model tương ứng nếu dự án đã tạo (ví dụ: Quiz, Question, QuizAttempt)
// use App\Models\Quiz;
// use App\Models\QuizAttempt;

class QuizController extends Controller
{
    // 1. Hiển thị giao diện làm bài Quiz
    public function show($id)
    {
        // Lấy thông tin quiz kèm theo các câu hỏi và đáp án
        // $quiz = Quiz::with('questions.answers')->findOrFail($id);
        
        // Tạm thời dùng mock data hoặc query DB trực tiếp nếu chưa có Model
        $quiz = DB::table('quizzes')->where('id', $id)->first();
        
        return view('quizzes.do-quiz', compact('quiz'));
    }

    // 2. Xử lý nộp bài và chấm điểm tự động
    public function submit(Request $request, $id)
    {
        // Giả sử $request->answers chứa mảng các đáp án học viên chọn dạng: [question_id => answer_id]
        $userAnswers = $request->input('answers', []);
        
        $score = 0;
        $totalQuestions = count($userAnswers);

        // Chấm điểm: So sánh đáp án của user với đáp án đúng trong DB
        foreach ($userAnswers as $questionId => $answerId) {
            $isCorrect = DB::table('answers')
                ->where('id', $answerId)
                ->where('question_id', $questionId)
                ->value('is_correct'); // Cột is_correct kiểu boolean (1 là đúng, 0 là sai)

            if ($isCorrect) {
                $score++;
            }
        }

        // Tính phần trăm và kiểm tra đậu/rớt (ví dụ 80% là qua)
        $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
        $isPassed = $percentage >= 80;

        // Lưu lịch sử làm bài vào bảng quiz_attempts
        DB::table('quiz_attempts')->insert([
            'user_id' => Auth::id(),
            'quiz_id' => $id,
            'score' => $score,
            'total_correct' => $score,
            'total_questions' => $totalQuestions,
           // 'is_passed' => $isPassed,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Chuyển hướng sang trang kết quả
        return redirect()->route('quiz.result', ['id' => $id])
            ->with('success', 'Nộp bài thành công! Bạn đạt ' . $score . '/' . $totalQuestions . ' điểm.');
    }

    // 3. Hiển thị trang kết quả
    public function result($id)
    {
        // Lấy kết quả mới nhất của user này
        $attempt = DB::table('quiz_attempts')
            ->where('quiz_id', $id)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();

        return view('quizzes.result', compact('attempt'));
    }
}