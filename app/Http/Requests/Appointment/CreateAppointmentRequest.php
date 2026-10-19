<?php

declare(strict_types=1);

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'       => [
                'required',
                'integer',
                Rule::exists('patients', 'id')->where('clinic_id', $this->user()->clinic_id),
            ],
            'doctor_id'        => [
                'required',
                'integer',
                Rule::exists('users', 'id')
                    ->where('clinic_id', $this->user()->clinic_id)
                    ->where('role', 'doctor')
                    ->where('is_active', true),
            ],
            'appointment_date' => ['required', 'date'],
            'is_urgent'        => ['sometimes', 'boolean'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}
