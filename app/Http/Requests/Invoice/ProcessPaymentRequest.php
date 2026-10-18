<?php

declare(strict_types=1);

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'method'      => ['required', 'in:cash,card,instapay'],
        ];
    }
}
