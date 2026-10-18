<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Note: 'role' is deliberately excluded; role is resolved exclusively from the database.
     */
    public function rules(): array
    {
        return [
            'username' => ['sometimes', 'string'],
            'login'    => ['sometimes', 'string'],
            'email'    => ['sometimes', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
        ];
    }
}
