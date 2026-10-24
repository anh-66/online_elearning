<?php

namespace App\Http\Requests\Admin;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
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
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'instructor_id' => [
                Rule::excludeIf(! $this->user()->isAdmin()),
                'required',
                Rule::exists('users', 'id')->whereIn('role', [UserRole::Admin->value, UserRole::Teacher->value]),
            ],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'level' => ['required', Rule::enum(CourseLevel::class)],
            'status' => ['required', Rule::enum(CourseStatus::class)],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:2048'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'tên khoá học',
            'category_id' => 'danh mục',
            'instructor_id' => 'giảng viên',
            'summary' => 'mô tả ngắn',
            'description' => 'nội dung chi tiết',
            'price' => 'học phí',
            'level' => 'trình độ',
            'status' => 'trạng thái',
            'thumbnail' => 'ảnh đại diện',
            'images' => 'thư viện ảnh',
            'images.*' => 'ảnh',
        ];
    }
}
