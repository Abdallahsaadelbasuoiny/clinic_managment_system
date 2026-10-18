<?php

declare(strict_types=1);

namespace App\Http\Requests\Appointment;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Appointment::ALL_STATUSES)],
            'notes'  => ['nullable', 'string', 'max:500'],
        ];
    }
}
