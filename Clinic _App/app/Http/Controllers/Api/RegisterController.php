<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\RegisterUserAction;
use App\DTOs\RegisterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    // استخدام دالة __invoke يجعل المتحكم مسؤولاً عن مسار واحد فقط (Single Action Controller)
    public function __invoke(
        RegisterRequest $request, 
        RegisterUserAction $action
    ): JsonResponse {
        
        // 1. تحويل الطلب إلى كائن بيانات آمن
        $data = RegisterData::fromRequest($request);

        // 2. تنفيذ عملية التسجيل
        $user = $action->execute($data);

        // 3. إرجاع الرد بصيغة JSON نظيفة ورمز الحالة 201 (تم الإنشاء)
        return response()->json([
            'message' => 'تم تسجيل الحساب بنجاح.',
            'data'    => new UserResource($user),
        ], 201);
    }
}