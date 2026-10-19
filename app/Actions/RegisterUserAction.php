<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function execute(RegisterData $data): User
    {
        // حفظ المستخدم الجديد في قاعدة البيانات وتشفير كلمة المرور
        return User::create([
            'email'    => $data->email,
            'password' => Hash::make($data->password),
        ]);
    }
}