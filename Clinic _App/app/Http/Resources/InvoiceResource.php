<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'clinic_id'           => $this->clinic_id,
            'patient_id'          => $this->patient_id,
            'subtotal'            => (float) $this->subtotal,
            'discount'            => (float) $this->discount,
            'discount_reason'     => $this->discount_reason,
            'total_amount'        => (float) $this->total_amount,
            'doctor_share'        => (float) $this->doctor_share,
            'clinic_share'        => (float) $this->clinic_share,
            'payment_status'      => $this->payment_status,
            'is_locked'           => (bool) $this->is_locked,
            'is_read_only'        => $this->isReadOnly(),
            'refund_reason'       => $this->refund_reason,
            'refunded_at'         => $this->refunded_at?->toIso8601String(),
            'refunded_by'         => $this->whenLoaded('refundedBy', fn() => [
                'id'   => $this->refundedBy->id,
                'name' => $this->refundedBy->name,
                'role' => $this->refundedBy->role,
            ]),
            'patient'             => $this->whenLoaded('patient', fn() => [
                'id'          => $this->patient->id,
                'name'        => $this->patient->name,
                'phone'       => $this->patient->phone,
                'national_id' => $this->patient->national_id,
            ]),
            'items'               => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments'            => $this->whenLoaded('payments', fn() => $this->payments->map(fn($p) => [
                'id'          => $p->id,
                'amount_paid' => (float) $p->amount_paid,
                'method'      => $p->method,
                'created_at'  => $p->created_at?->toIso8601String(),
            ])),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
