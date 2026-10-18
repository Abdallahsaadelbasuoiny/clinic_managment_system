<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\ApplyDiscountRequest;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\ProcessPaymentRequest;
use App\Http\Requests\Invoice\RefundInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * List invoices with filters for payment_status, patient, and date.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::query()
            ->with(['patient', 'items', 'payments', 'refundedBy'])
            ->latest();

        if ($status = $request->query('payment_status')) {
            $query->where('payment_status', $status);
        }

        if ($patientId = $request->query('patient_id')) {
            $query->where('patient_id', $patientId);
        }

        if ($clinicId = $request->query('clinic_id')) {
            $query->where('clinic_id', $clinicId);
        }

        $invoices = $query->paginate((int) $request->query('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data'   => InvoiceResource::collection($invoices),
            'meta'   => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'total'        => $invoices->total(),
            ],
        ], 200);
    }

    /**
     * Create a new invoice with initial items and optional discount.
     */
    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $user = $request->user();

        $invoice = DB::transaction(function () use ($request, $user) {
            $itemsData = $request->validated('items');
            $subtotal = 0.0;

            foreach ($itemsData as $item) {
                $subtotal += ((float) $item['unit_price']) * ((int) $item['quantity']);
            }

            $discount = (float) $request->validated('discount', 0.00);
            $totalAmount = max(0.00, $subtotal - $discount);

            $invoice = Invoice::create([
                'clinic_id'        => $request->input('clinic_id', $user->clinic_id),
                'patient_id'       => $request->validated('patient_id'),
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'discount_reason'  => $request->validated('discount_reason'),
                'total_amount'     => $totalAmount,
                'doctor_share'     => $request->validated('doctor_share', 0.00),
                'clinic_share'     => $request->validated('clinic_share', 0.00),
                'payment_status'   => Invoice::STATUS_UNPAID,
                'is_locked'        => false,
            ]);

            foreach ($itemsData as $item) {
                $unitPrice = (float) $item['unit_price'];
                $qty = (int) $item['quantity'];
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'description' => $item['description'],
                    'quantity'    => $qty,
                    'unit_price'  => $unitPrice,
                    'total_price' => $unitPrice * $qty,
                ]);
            }

            return $invoice;
        });

        $invoice->load(['patient', 'items', 'payments']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Invoice created successfully.',
            'data'    => new InvoiceResource($invoice),
        ], 201);
    }

    /**
     * Show invoice details (includes is_read_only state).
     */
    public function show(int $id): JsonResponse
    {
        $invoice = Invoice::with(['patient', 'items', 'payments', 'refundedBy'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => new InvoiceResource($invoice),
        ], 200);
    }

    /**
     * Update invoice items and discounts.
     * Programmatically locked (read-only) once paid.
     */
    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->isReceptionist() && !$user->isAdmin() && !$user->isOwner()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. Only receptionists are authorized to modify invoice items.',
            ], 403);
        }

        $invoice = Invoice::with('items')->findOrFail($id);

        // Strict Lock Check: Paid invoices are Read-Only
        if ($invoice->isReadOnly()) {
            return response()->json([
                'status'         => 'error',
                'message'        => 'This invoice is paid and locked as Read-Only. Editing is strictly prohibited.',
                'payment_status' => $invoice->payment_status,
                'is_locked'      => true,
            ], 422);
        }

        DB::transaction(function () use ($request, $invoice) {
            // Update items if provided
            if ($request->has('items')) {
                $invoice->items()->delete();
                $subtotal = 0.0;
                foreach ($request->validated('items') as $item) {
                    $unitPrice = (float) $item['unit_price'];
                    $qty = (int) $item['quantity'];
                    $lineTotal = $unitPrice * $qty;
                    $subtotal += $lineTotal;

                    InvoiceItem::create([
                        'invoice_id'  => $invoice->id,
                        'description' => $item['description'],
                        'quantity'    => $qty,
                        'unit_price'  => $unitPrice,
                        'total_price' => $lineTotal,
                    ]);
                }
                $invoice->subtotal = $subtotal;
            }

            if ($request->has('discount')) {
                $invoice->discount = (float) $request->validated('discount');
            }

            if ($request->has('discount_reason')) {
                $invoice->discount_reason = $request->validated('discount_reason');
            }

            if ($request->has('doctor_share')) {
                $invoice->doctor_share = (float) $request->validated('doctor_share');
            }

            if ($request->has('clinic_share')) {
                $invoice->clinic_share = (float) $request->validated('clinic_share');
            }

            // Recalculate total amount
            $invoice->total_amount = max(0.00, ((float) $invoice->subtotal) - ((float) $invoice->discount));
            $invoice->save();
        });

        $invoice->load(['patient', 'items', 'payments']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Invoice updated successfully.',
            'data'    => new InvoiceResource($invoice),
        ], 200);
    }

    /**
     * Apply a discount before payment (Receptionist capability).
     */
    public function applyDiscount(ApplyDiscountRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->isReceptionist() && !$user->isAdmin() && !$user->isOwner()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. Only receptionists are authorized to apply discounts.',
            ], 403);
        }

        $invoice = Invoice::findOrFail($id);

        if ($invoice->isReadOnly()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot apply discounts to an invoice that is already paid or locked.',
            ], 422);
        }

        $discount = (float) $request->validated('discount');
        $invoice->discount = $discount;
        $invoice->discount_reason = $request->validated('discount_reason');
        $invoice->total_amount = max(0.00, ((float) $invoice->subtotal) - $discount);
        $invoice->save();

        $invoice->load(['patient', 'items', 'payments']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Discount applied successfully.',
            'data'    => new InvoiceResource($invoice),
        ], 200);
    }

    /**
     * Record a payment on the invoice.
     * When paid in full, status becomes 'paid' and invoice is locked to read-only.
     */
    public function pay(ProcessPaymentRequest $request, int $id): JsonResponse
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->payment_status === Invoice::STATUS_PAID) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invoice is already fully paid and locked.',
            ], 422);
        }

        $amount = (float) $request->validated('amount_paid');
        $method = $request->validated('method');
        $user = $request->user();

        DB::transaction(function () use ($invoice, $amount, $method, $user) {
            Payment::create([
                'clinic_id'           => $invoice->clinic_id,
                'invoice_id'          => $invoice->id,
                'amount_paid'         => $amount,
                'method'              => $method,
                'received_by_user_id' => $user->id,
            ]);

            $totalPaid = (float) $invoice->payments()->sum('amount_paid') + $amount;

            if ($totalPaid >= (float) $invoice->total_amount) {
                // Fully Paid -> automatically lock into Read-only mode
                $invoice->payment_status = Invoice::STATUS_PAID;
                $invoice->is_locked = true;
            } else {
                $invoice->payment_status = Invoice::STATUS_PARTIALLY_PAID;
            }

            $invoice->save();
        });

        $invoice->load(['patient', 'items', 'payments']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment processed successfully. Invoice is now ' . ($invoice->is_locked ? 'fully paid and locked (Read-Only).' : 'partially paid.'),
            'data'    => new InvoiceResource($invoice),
        ], 200);
    }

    /**
     * Process a refund for a paid invoice (Receptionist exclusive).
     */
    public function refund(RefundInvoiceRequest $request, int $id): JsonResponse
    {
        $user = $request->user();

        // Ensure only Receptionists can trigger refunds
        if (!$user->isReceptionist()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. Only receptionists are authorized to process refunds.',
            ], 403);
        }

        $invoice = Invoice::findOrFail($id);

        if ($invoice->payment_status === Invoice::STATUS_UNPAID) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot refund an unpaid invoice.',
            ], 422);
        }

        if ($invoice->payment_status === Invoice::STATUS_REFUNDED) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This invoice has already been refunded.',
            ], 422);
        }

        $invoice->payment_status = Invoice::STATUS_REFUNDED;
        $invoice->refund_reason = $request->validated('reason');
        $invoice->refunded_at = now();
        $invoice->refunded_by_user_id = $user->id;
        $invoice->is_locked = true;
        $invoice->save();

        $invoice->load(['patient', 'items', 'payments', 'refundedBy']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Invoice refund processed successfully.',
            'data'    => new InvoiceResource($invoice),
        ], 200);
    }
}
