<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clinicId = $this->user()->clinic_id;

        return [
            'patient_id' => [
                'required',
                'integer',
                Rule::exists('patients', 'id')->where('clinic_id', $clinicId),
            ],
            'appointment_id' => [
                'nullable',
                'integer',
                Rule::exists('appointments', 'id')
                    ->where('clinic_id', $clinicId)
                    ->where('doctor_id', $this->user()->id)
                    ->where('patient_id', $this->input('patient_id')),
            ],
            'visit_date' => ['sometimes', 'date'],
            'chief_complaint' => ['nullable', 'string', 'max:5000'],
            'medical_history' => ['nullable', 'string', 'max:10000'],
            'examination' => ['nullable', 'string', 'max:10000'],
            'diagnosis' => ['nullable', 'string', 'max:5000'],
            'treatment_plan' => ['nullable', 'string', 'max:10000'],
            'follow_up_at' => ['nullable', 'date', 'after:visit_date'],
        ];
    }
}
