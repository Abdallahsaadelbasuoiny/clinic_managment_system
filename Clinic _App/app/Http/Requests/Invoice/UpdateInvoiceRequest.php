<?php

declare(strict_types=1);

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'discount_reason'     => ['nullable', 'string', 'max:255'],
            'doctor_share'        => ['nullable', 'numeric', 'min:0'],
            'clinic_share'        => ['nullable', 'numeric', 'min:0'],
            'items'               => ['sometimes', 'array'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity'    => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required_with:items', 'numeric', 'min:0'],
        ];
    }
}
