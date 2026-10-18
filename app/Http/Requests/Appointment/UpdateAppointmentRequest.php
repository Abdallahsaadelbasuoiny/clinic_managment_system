<?php

declare(strict_types=1);

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

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
            'doctor_id'        => ['sometimes', 'integer', 'exists:users,id'],
            'is_urgent'        => ['sometimes', 'boolean'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}
