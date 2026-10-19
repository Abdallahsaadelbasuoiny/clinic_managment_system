<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiagnosticOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medical_record_id' => $this->medical_record_id,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'type' => $this->type,
            'test_name' => $this->test_name,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'result' => $this->result,
            'ordered_at' => $this->ordered_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}
