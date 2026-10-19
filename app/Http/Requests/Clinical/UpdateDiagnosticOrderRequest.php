<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use App\Models\DiagnosticOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiagnosticOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'required', 'string', Rule::in(DiagnosticOrder::STATUSES)],
            'result' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
