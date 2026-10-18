<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'clinic_id'           => $this->clinic_id,
            'name'                => $this->name,
            'phone'               => $this->phone,
            'national_id'         => $this->national_id,
            'date_of_birth'       => $this->date_of_birth?->format('Y-m-d'),
            'gender'              => $this->gender,
            'address'             => $this->address,
            'created_by_user_id'  => $this->created_by_user_id,
            'created_by'          => $this->whenLoaded('createdBy', fn() => [
                'id'   => $this->createdBy->id,
                'name' => $this->createdBy->name,
                'role' => $this->createdBy->role,
            ]),
            'created_at'          => $this->created_at?->toIso8601String(),
        ];
    }
}
