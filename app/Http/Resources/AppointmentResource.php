<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'clinic_id'          => $this->clinic_id,
            'patient_id'         => $this->patient_id,
            'doctor_id'          => $this->doctor_id,
            'appointment_date'   => $this->appointment_date?->toIso8601String(),
            'status'             => $this->status,
            'is_urgent'          => (bool) $this->is_urgent,
            'notes'              => $this->notes,
            'patient'            => $this->whenLoaded('patient', fn() => [
                'id'          => $this->patient->id,
                'name'        => $this->patient->name,
                'phone'       => $this->patient->phone,
                'national_id' => $this->patient->national_id,
            ]),
            'doctor'             => $this->whenLoaded('doctor', fn() => [
                'id'    => $this->doctor->id,
                'name'  => $this->doctor->name,
                'email' => $this->doctor->email,
                'phone' => $this->doctor->phone,
            ]),
            'created_by'         => $this->whenLoaded('createdBy', fn() => [
                'id'   => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'role' => $this->createdBy->role,
            ]),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
