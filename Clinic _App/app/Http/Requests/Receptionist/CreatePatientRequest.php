<?php

declare(strict_types=1);

namespace App\Http\Requests\Receptionist;

use Illuminate\Foundation\Http\FormRequest;

class CreatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:50'],
            'national_id'   => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'gender'        => ['nullable', 'in:male,female'],
            'address'       => ['nullable', 'string', 'max:500'],
            'clinic_id'     => ['nullable', 'integer', 'exists:clinics,id'],
        ];
    }
}
