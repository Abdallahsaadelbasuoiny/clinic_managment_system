<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClinicSettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $clinic = $request->user()?->clinic;

        if (!$clinic) {
            $subdomain = explode('.', $request->getHost())[0];
            $clinic = Clinic::query()
                ->where('status', 'active')
                ->where('subdomain', $subdomain)
                ->first();
        }

        $clinic ??= Clinic::query()->where('status', 'active')->orderBy('id')->first();

        if (!$clinic) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active clinic is configured.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->clinicData($clinic),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $clinic = $request->user()->clinic;

        if (!$clinic) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is not assigned to a clinic.',
            ], 422);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'services' => ['required', 'array', 'max:100'],
            'services.*.name' => ['required', 'string', 'max:255'],
            'services.*.description' => ['nullable', 'string', 'max:2000'],
            'services.*.price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $clinic->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Clinic settings saved successfully.',
            'data' => $this->clinicData($clinic->refresh()),
        ]);
    }

    private function clinicData(Clinic $clinic): array
    {
        return [
            'id' => $clinic->id,
            'name' => $clinic->name,
            'phone' => $clinic->phone,
            'address' => $clinic->address,
            'services' => $clinic->services ?? [],
        ];
    }
}
