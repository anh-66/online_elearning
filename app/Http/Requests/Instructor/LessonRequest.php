<?php

namespace App\Http\Requests\Instructor;

use App\Models\Lesson;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Tự động đồng bộ link YouTube từ CKEditor sang input video_url trước khi validate
     */
    protected function prepareForValidation(): void
    {
        if (blank($this->input('video_url')) && filled($this->input('content'))) {
            $extracted = Lesson::extractYouTubeUrl($this->input('content'));
            if ($extracted) {
                $this->merge(['video_url' => $extracted]);
            }
        }
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
