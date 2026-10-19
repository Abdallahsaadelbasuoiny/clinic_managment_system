<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateDoctorRequest;
use App\Http\Requests\Admin\CreateReceptionistRequest;
use App\Http\Resources\AuthUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * List all doctors.
     */
    public function listDoctors(Request $request): JsonResponse
    {
        $doctors = User::query()
            ->where('role', User::ROLE_DOCTOR)
            ->where('clinic_id', $request->user()->clinic_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => AuthUserResource::collection($doctors),
        ], 200);
    }

    /**
     * Create a doctor account (Admin exclusive).
     */
    public function createDoctor(CreateDoctorRequest $request): JsonResponse
    {
        $admin = $request->user();

        $doctor = User::create([
            'clinic_id'            => $admin->clinic_id,
            'email'                => $request->validated('email'),
            'phone'                => $request->validated('phone'),
            'password'             => Hash::make($request->validated('password')),
            'role'                 => User::ROLE_DOCTOR,
            'is_active'            => true,
            'must_change_password' => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Doctor profile created successfully.',
            'data'    => new AuthUserResource($doctor),
        ], 201);
    }

    /**
     * List all receptionists.
     */
    public function listReceptionists(Request $request): JsonResponse
    {
        $receptionists = User::query()
            ->where('role', User::ROLE_RECEPTIONIST)
            ->where('clinic_id', $request->user()->clinic_id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => AuthUserResource::collection($receptionists),
        ], 200);
    }

    /**
     * Create a receptionist account (Admin exclusive).
     */
    public function createReceptionist(CreateReceptionistRequest $request): JsonResponse
    {
        $admin = $request->user();

        $receptionist = User::create([
            'clinic_id'            => $admin->clinic_id,
            'email'                => $request->validated('email'),
            'phone'                => $request->validated('phone'),
            'password'             => Hash::make($request->validated('password')),
            'role'                 => User::ROLE_RECEPTIONIST,
            'is_active'            => true,
            'must_change_password' => true,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Receptionist profile created successfully.',
            'data'    => new AuthUserResource($receptionist),
        ], 201);
    }

    public function updateStaff(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();
        $staff = User::query()
            ->where('clinic_id', $admin->clinic_id)
            ->whereIn('role', [User::ROLE_DOCTOR, User::ROLE_RECEPTIONIST])
            ->findOrFail($id);

        $validated = $request->validate([
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
        ]);

        if (filled($validated['password'] ?? null)) {
            $validated['password'] = Hash::make($validated['password']);
            $validated['must_change_password'] = true;
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Staff profile updated successfully.',
            'data' => new AuthUserResource($staff->refresh()),
        ]);
    }

    public function listClinicDoctors(Request $request): JsonResponse
    {
        $doctors = User::query()
            ->where('role', User::ROLE_DOCTOR)
            ->where('clinic_id', $request->user()->clinic_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => AuthUserResource::collection($doctors),
        ]);
    }
}
