<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinical\CreateDiagnosticOrderRequest;
use App\Http\Requests\Clinical\CreateMedicalRecordRequest;
use App\Http\Requests\Clinical\CreatePrescriptionRequest;
use App\Http\Requests\Clinical\UpdateDiagnosticOrderRequest;
use App\Http\Resources\DiagnosticOrderResource;
use App\Http\Resources\MedicalRecordResource;
use App\Http\Resources\PrescriptionResource;
use App\Models\DiagnosticOrder;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClinicalRecordController extends Controller
{
    public function indexForPatient(Request $request, int $patientId): JsonResponse
    {
        Patient::query()->findOrFail($patientId);

        $records = MedicalRecord::query()
            ->with(['patient', 'doctor', 'prescriptions.items', 'diagnosticOrders'])
            ->where('patient_id', $patientId)
            ->latest('visit_date')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => MedicalRecordResource::collection($records),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function store(CreateMedicalRecordRequest $request): JsonResponse
    {
        $user = $request->user();
        $record = MedicalRecord::create([
            ...$request->validated(),
            'clinic_id' => $user->clinic_id,
            'doctor_id' => $user->id,
            'visit_date' => $request->validated('visit_date', now()),
        ]);

        $record->load(['patient', 'doctor', 'prescriptions.items', 'diagnosticOrders']);

        return response()->json([
            'status' => 'success',
            'message' => 'Medical record created.',
            'data' => new MedicalRecordResource($record),
        ], 201);
    }

    public function storePrescription(CreatePrescriptionRequest $request, int $recordId): JsonResponse
    {
        $user = $request->user();
        $record = MedicalRecord::query()
            ->where('doctor_id', $user->id)
            ->findOrFail($recordId);

        $prescription = DB::transaction(function () use ($request, $record, $user): Prescription {
            $prescription = Prescription::create([
                'clinic_id' => $user->clinic_id,
                'medical_record_id' => $record->id,
                'patient_id' => $record->patient_id,
                'doctor_id' => $user->id,
                'status' => 'issued',
                'notes' => $request->validated('notes'),
                'issued_at' => now(),
            ]);

            $prescription->items()->createMany($request->validated('items'));

            return $prescription;
        });

        $prescription->load('items');

        return response()->json([
            'status' => 'success',
            'message' => 'Prescription issued.',
            'data' => new PrescriptionResource($prescription),
        ], 201);
    }

    public function storeDiagnosticOrder(CreateDiagnosticOrderRequest $request, int $recordId): JsonResponse
    {
        $user = $request->user();
        $record = MedicalRecord::query()
            ->where('doctor_id', $user->id)
            ->findOrFail($recordId);

        $order = DiagnosticOrder::create([
            ...$request->validated(),
            'clinic_id' => $user->clinic_id,
            'medical_record_id' => $record->id,
            'patient_id' => $record->patient_id,
            'doctor_id' => $user->id,
            'status' => 'ordered',
            'ordered_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Diagnostic order created.',
            'data' => new DiagnosticOrderResource($order),
        ], 201);
    }

    public function indexDiagnosticOrders(Request $request, int $patientId): JsonResponse
    {
        Patient::query()->findOrFail($patientId);
        $orders = DiagnosticOrder::query()
            ->where('patient_id', $patientId)
            ->latest('ordered_at')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => DiagnosticOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function updateDiagnosticOrder(UpdateDiagnosticOrderRequest $request, int $orderId): JsonResponse
    {
        $order = DiagnosticOrder::query()
            ->where('doctor_id', $request->user()->id)
            ->findOrFail($orderId);
        $validated = $request->validated();
        $newStatus = $validated['status'] ?? $order->status;

        if (!$order->canTransitionTo($newStatus)) {
            return response()->json([
                'status' => 'error',
                'message' => "Invalid diagnostic order transition from '{$order->status}' to '{$newStatus}'.",
            ], 422);
        }

        if ($order->status === 'completed' || $order->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'A completed or cancelled diagnostic order cannot be changed.',
            ], 422);
        }

        if ($newStatus === 'completed' && blank($validated['result'] ?? $order->result)) {
            return response()->json([
                'status' => 'error',
                'message' => 'A result is required to complete a diagnostic order.',
            ], 422);
        }

        $order->fill($validated);
        $order->status = $newStatus;
        if ($newStatus === 'completed') {
            $order->completed_at = now();
        }
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Diagnostic order updated.',
            'data' => new DiagnosticOrderResource($order),
        ]);
    }
}
