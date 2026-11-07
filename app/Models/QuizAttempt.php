<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'quiz_id', 'score', 'total_correct', 'total_questions', 'is_passed', 'selected_answers'])]
class QuizAttempt extends Model
{
    use HasFactory;

    /**
     * Điểm phần trăm tối thiểu để đạt bài quiz.
     */
    public const PASSING_SCORE = 80;

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'total_correct' => 'integer',
            'total_questions' => 'integer',
            'is_passed' => 'boolean',
            'selected_answers' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Fallback cho total_questions nếu database chưa có cột này.
     */
    protected function totalQuestions(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->quiz?->questions()->count() ?? 0,
        );
    }

    /**
     * Fallback cho total_correct nếu database chưa có cột này.
     */
    protected function totalCorrect(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value !== null) {
                    return (int) $value;
                }
                $total = $this->total_questions ?: 1;

                return (int) round(($this->score / 100) * $total);
            },
        );
    }

    /**
     * Fallback cho is_passed nếu database chưa có cột này.
     */
    protected function isPassed(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null ? (bool) $value : ($this->score >= self::PASSING_SCORE),
        );
    }

    /**
     * Mã đáp án học viên đã chọn cho một câu hỏi (null nếu bỏ trống).
     */
    public function selectedAnswerFor(Question $question): ?int
    {
        $selected = $this->selected_answers;

        if (is_string($selected)) {
            $selected = json_decode($selected, true);
        }

        if (! is_array($selected)) {
            // Nếu đạt 100% thì tất cả câu hỏi đều đã chọn đáp án đúng
            if ($this->score === 100) {
                $correct = $question->answers->firstWhere('is_correct', true);

                return $correct ? (int) $correct->id : null;
            }

            return null;
        }

        $answerId = $selected[$question->id] ?? $selected[(string) $question->id] ?? null;

        return $answerId === null ? null : (int) $answerId;
    }
}
