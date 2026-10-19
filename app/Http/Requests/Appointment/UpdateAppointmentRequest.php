<?php

declare(strict_types=1);

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => ['sometimes', 'date'],
            'doctor_id'        => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id')
                    ->where('clinic_id', $this->user()->clinic_id)
                    ->where('role', 'doctor')
                    ->where('is_active', true),
            ],
            'is_urgent'        => ['sometimes', 'boolean'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}
