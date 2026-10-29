<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
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
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'level' => ['required', Rule::in([Course::LEVEL_BEGINNER, Course::LEVEL_INTERMEDIATE, Course::LEVEL_ADVANCED])],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'tên khóa học',
            'category_id' => 'danh mục',
            'description' => 'mô tả',
            'price' => 'học phí',
            'level' => 'trình độ',
            'thumbnail' => 'ảnh đại diện',
            'images' => 'thư viện ảnh',
            'images.*' => 'ảnh thư viện',
        ];
    }
}
