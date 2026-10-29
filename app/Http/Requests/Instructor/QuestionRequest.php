<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string', 'max:2000'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'answers' => ['required', 'array', 'min:2', 'max:6'],
            'answers.*' => ['required', 'string', 'max:1000'],
            'correct_answer' => ['required', 'integer', Rule::in(array_keys((array) $this->input('answers', [])))],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'question_text' => 'nội dung câu hỏi',
            'explanation' => 'giải thích',
            'answers' => 'danh sách đáp án',
            'answers.*' => 'đáp án',
            'correct_answer' => 'đáp án đúng',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'correct_answer.required' => 'Vui lòng chọn một đáp án đúng.',
            'correct_answer.in' => 'Đáp án đúng không hợp lệ.',
        ];
    }
}
