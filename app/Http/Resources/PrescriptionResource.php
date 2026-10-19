<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medical_record_id' => $this->medical_record_id,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'status' => $this->status,
            'notes' => $this->notes,
            'issued_at' => $this->issued_at?->toIso8601String(),
            'items' => $this->whenLoaded('items'),
        ];
    }
}
