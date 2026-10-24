<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:1000'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_preview' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'tên bài học',
            'content' => 'nội dung',
            'video_url' => 'đường dẫn video',
            'duration_minutes' => 'thời lượng',
            'position' => 'thứ tự',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_preview' => $this->boolean('is_preview')]);
    }
}
