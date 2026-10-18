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

class AdminUserController extends Controller
{
    /**
     * List all doctors.
     */
    public function listDoctors(Request $request): JsonResponse
    {
        $doctors = User::query()
            ->where('role', User::ROLE_DOCTOR)
            ->when($request->query('clinic_id'), fn($q, $cid) => $q->where('clinic_id', $cid))
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
            'clinic_id'            => $request->input('clinic_id', $admin->clinic_id),
            'name'                 => $request->validated('name'),
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
            ->when($request->query('clinic_id'), fn($q, $cid) => $q->where('clinic_id', $cid))
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
            'clinic_id'            => $request->input('clinic_id', $admin->clinic_id),
            'name'                 => $request->validated('name'),
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
}
