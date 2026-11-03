<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'bio' => ['nullable', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'avatar_generated' => ['nullable', 'string', 'max:7000000'],
        ];
    }

    /**
     * Validate the generated avatar payload as a PNG image.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $payload = $this->input('avatar_generated');

                if (! is_string($payload) || $payload === '') {
                    return;
                }

                if (! preg_match('/^data:image\/png;base64,(?<data>[A-Za-z0-9+\/=\r\n]+)$/', $payload, $matches)) {
                    $validator->errors()->add('avatar_generated', 'Ảnh avatar thiết kế không hợp lệ.');

                    return;
                }

                $image = base64_decode($matches['data'], true);

                if ($image === false || getimagesizefromstring($image) === false) {
                    $validator->errors()->add('avatar_generated', 'Ảnh avatar thiết kế không hợp lệ.');
                }
            },
        ];
    }
}
