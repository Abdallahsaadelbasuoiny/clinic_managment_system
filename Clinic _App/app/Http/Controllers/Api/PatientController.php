<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\CreatePatientRequest;
use App\Http\Requests\Receptionist\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * List patients with search/filter capabilities.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Patient::query()->with('createdBy');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        if ($request->query('clinic_id')) {
            $query->where('clinic_id', $request->query('clinic_id'));
        }

        $patients = $query->latest()->paginate((int) $request->query('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data'   => PatientResource::collection($patients),
            'meta'   => [
                'current_page' => $patients->currentPage(),
                'last_page'    => $patients->lastPage(),
                'total'        => $patients->total(),
            ],
        ], 200);
    }

    /**
     * Create a new patient profile (Receptionist exclusive).
     */
    public function store(CreatePatientRequest $request): JsonResponse
    {
        $user = $request->user();

        $patient = Patient::create([
            'clinic_id'          => $request->input('clinic_id', $user->clinic_id),
            'name'               => $request->validated('name'),
            'phone'              => $request->validated('phone'),
            'national_id'        => $request->validated('national_id'),
            'date_of_birth'      => $request->validated('date_of_birth'),
            'gender'             => $request->validated('gender'),
            'address'            => $request->validated('address'),
            'created_by_user_id' => $user->id,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Patient profile created successfully.',
            'data'    => new PatientResource($patient),
        ], 201);
    }

    /**
     * Show a patient with their details.
     */
    public function show(int $id): JsonResponse
    {
        $patient = Patient::with(['createdBy', 'appointments.doctor'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new PatientResource($patient),
        ], 200);
    }

    /**
     * Update patient details.
     */
    public function update(UpdatePatientRequest $request, int $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);
        $patient->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Patient updated successfully.',
            'data'    => new PatientResource($patient),
        ], 200);
    }
}
