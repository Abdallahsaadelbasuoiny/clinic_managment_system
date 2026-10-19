<?php

declare(strict_types=1);

namespace App\Http\Requests\Clinical;

use App\Models\DiagnosticOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDiagnosticOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(DiagnosticOrder::TYPES)],
            'test_name' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
