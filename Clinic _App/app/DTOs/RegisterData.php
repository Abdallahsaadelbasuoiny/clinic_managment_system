<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\RegisterRequest;

readonly class RegisterData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}

    // دالة مساعدة لتحويل طلب المستخدم إلى كائن البيانات مباشرة
    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password')
        );
    }
}