<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate user with credentials only (no role input in request).
     * The role is automatically retrieved from the database.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->input('username') ?? $request->input('login') ?? $request->input('email');
        $password = (string) $request->input('password');

        if (!$login) {
            throw ValidationException::withMessages([
                'email' => ['The username, email, or login identifier field is required.'],
            ]);
        }

        // Search user by email, phone, or name (username)
        $user = User::query()
            ->where('email', $login)
            ->orWhere('phone', $login)
            ->orWhere('name', $login)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid email/phone or password.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Your account is currently deactivated. Please contact administration.',
            ], 403);
        }

        // Role is extracted strictly from the database record
        $role = $user->role;

        // Generate Sanctum Personal Access Token with role-based claims/abilities
        $abilities = [
            'role:' . $role,
            '*',
        ];

        $token = $user->createToken('auth-token', $abilities)->plainTextToken;

        return response()->json([
            'status'     => 'success',
            'message'    => 'Authentication successful.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => new AuthUserResource($user),
        ], 200);
    }

    /**
     * Logout and revoke current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Get profile of the currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user'   => new AuthUserResource($request->user()),
        ], 200);
    }
}
