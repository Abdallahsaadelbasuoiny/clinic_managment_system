<?php

declare(strict_types=1);

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'        => ['required', 'integer', 'exists:patients,id'],
            'clinic_id'         => ['nullable', 'integer', 'exists:clinics,id'],
            'discount'          => ['nullable', 'numeric', 'min:0'],
            'discount_reason'   => ['nullable', 'string', 'max:255'],
            'doctor_share'      => ['nullable', 'numeric', 'min:0'],
            'clinic_share'      => ['nullable', 'numeric', 'min:0'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
        ];
    }
}
