<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'chapter_id' => ['sometimes', 'integer'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:500', 'regex:/^https?:\/\/(www\.|m\.)?(youtube\.com|youtu\.be)\//i'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt', 'max:20480'],
            'remove_attachment' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'tên bài học',
            'chapter_id' => 'chương',
            'content' => 'nội dung',
            'video_url' => 'link video YouTube',
            'attachment' => 'tài liệu đính kèm',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'video_url.regex' => 'Link video phải là link YouTube (youtube.com hoặc youtu.be).',
        ];
    }
}
