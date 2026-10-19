<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'appointment_id' => $this->appointment_id,
            'doctor_id' => $this->doctor_id,
            'visit_date' => $this->visit_date?->toIso8601String(),
            'chief_complaint' => $this->chief_complaint,
            'medical_history' => $this->medical_history,
            'examination' => $this->examination,
            'diagnosis' => $this->diagnosis,
            'treatment_plan' => $this->treatment_plan,
            'follow_up_at' => $this->follow_up_at?->toIso8601String(),
            'patient' => $this->whenLoaded('patient', fn () => [
                'id' => $this->patient->id,
                'name' => $this->patient->name,
            ]),
            'doctor' => $this->whenLoaded('doctor', fn () => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->name,
            ]),
            'prescriptions' => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
            'diagnostic_orders' => DiagnosticOrderResource::collection($this->whenLoaded('diagnosticOrders')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
