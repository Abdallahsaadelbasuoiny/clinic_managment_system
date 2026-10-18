<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'clinic_id'            => $this->clinic_id,
            'name'                 => $this->name,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'role'                 => $this->role,
            'is_active'            => (bool) $this->is_active,
            'must_change_password' => (bool) $this->must_change_password,
            'created_at'           => $this->created_at?->toIso8601String(),
        ];
    }
}
