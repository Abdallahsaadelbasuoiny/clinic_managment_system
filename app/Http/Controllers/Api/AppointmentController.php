<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentStatusRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * List appointments with filters for status, urgency, doctor, and patient.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::query()
            ->with(['patient', 'doctor', 'createdBy'])
            ->latest('appointment_date');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($request->has('is_urgent')) {
            $query->where('is_urgent', filter_var($request->query('is_urgent'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($doctorId = $request->query('doctor_id')) {
            $query->where('doctor_id', $doctorId);
        }

        if ($patientId = $request->query('patient_id')) {
            $query->where('patient_id', $patientId);
        }

        if ($date = $request->query('date')) {
            $query->whereDate('appointment_date', $date);
        }

        if ($clinicId = $request->query('clinic_id')) {
            $query->where('clinic_id', $clinicId);
        }

        $appointments = $query->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data'   => AppointmentResource::collection($appointments),
            'meta'   => [
                'current_page' => $appointments->currentPage(),
                'last_page'    => $appointments->lastPage(),
                'total'        => $appointments->total(),
            ],
        ], 200);
    }

    /**
     * Create an appointment (defaults to 'pending' status, supports urgent flag).
     */
    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        $user = $request->user();

        $appointment = Appointment::create([
            'clinic_id'          => $request->input('clinic_id', $user->clinic_id),
            'patient_id'         => $request->validated('patient_id'),
            'doctor_id'          => $request->validated('doctor_id'),
            'appointment_date'   => $request->validated('appointment_date'),
            'status'             => Appointment::STATUS_PENDING,
            'is_urgent'          => (bool) $request->validated('is_urgent', false),
            'notes'              => $request->validated('notes'),
            'created_by_user_id' => $user->id,
        ]);

        $appointment->load(['patient', 'doctor', 'createdBy']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment scheduled successfully.',
            'data'    => new AppointmentResource($appointment),
        ], 201);
    }

    /**
     * Show appointment details.
     */
    public function show(int $id): JsonResponse
    {
        $appointment = Appointment::with(['patient', 'doctor', 'createdBy'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new AppointmentResource($appointment),
        ], 200);
    }

    /**
     * Update appointment details.
     */
    public function update(UpdateAppointmentRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        if (in_array($appointment->status, [Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED], true)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Cannot modify an appointment that is already {$appointment->status}.",
            ], 422);
        }

        $appointment->update($request->validated());
        $appointment->load(['patient', 'doctor', 'createdBy']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment details updated successfully.',
            'data'    => new AppointmentResource($appointment),
        ], 200);
    }

    /**
     * Transition appointment status through lifecycle:
     * Pending -> Confirmed -> Completed -> Cancelled
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $newStatus = $request->validated('status');

        if (!$appointment->canTransitionTo($newStatus)) {
            return response()->json([
                'status'  => 'error',
                'message' => "Invalid status transition from '{$appointment->status}' to '{$newStatus}'.",
                'current_status' => $appointment->status,
                'target_status'  => $newStatus,
            ], 422);
        }

        $appointment->status = $newStatus;
        if ($notes = $request->validated('notes')) {
            $appointment->notes = ($appointment->notes ? $appointment->notes . "\n" : '') . "[Status -> {$newStatus}]: " . $notes;
        }
        $appointment->save();

        $appointment->load(['patient', 'doctor', 'createdBy']);

        return response()->json([
            'status'  => 'success',
            'message' => "Appointment status successfully updated to '{$newStatus}'.",
            'data'    => new AppointmentResource($appointment),
        ], 200);
    }

    /**
     * Cancel an appointment.
     */
    public function destroy(int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = Appointment::STATUS_CANCELLED;
        $appointment->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment has been cancelled.',
        ], 200);
    }
}
