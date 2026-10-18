<?php

declare(strict_types=1);

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'       => ['required', 'integer', 'exists:patients,id'],
            'doctor_id'        => ['required', 'integer', 'exists:users,id'],
            'appointment_date' => ['required', 'date'],
            'is_urgent'        => ['sometimes', 'boolean'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'clinic_id'        => ['nullable', 'integer', 'exists:clinics,id'],
        ];
    }
}
