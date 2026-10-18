<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceLifecycleAndRefundTest extends TestCase
{
    use RefreshDatabase;

    private User $receptionist;
    private User $doctor;
    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receptionist = User::create([
            'name'      => 'name 1',
            'email'     => 'receptionist@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        $this->doctor = User::create([
            'name'      => 'name 2',
            'email'     => 'doctor@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_DOCTOR,
            'is_active' => true,
        ]);

        $this->patient = Patient::create([
            'name'  => 'name 3',
            'phone' => '01099998888',
        ]);
    }

    public function test_receptionist_can_create_invoice_with_items_and_discount(): void
    {
        Sanctum::actingAs($this->receptionist);

        $response = $this->postJson('/api/invoices', [
            'patient_id'      => $this->patient->id,
            'discount'        => 50.00,
            'discount_reason' => 'First visit voucher',
            'items'           => [
                [
                    'description' => 'Consultation',
                    'quantity'    => 1,
                    'unit_price'  => 300.00,
                ],
                [
                    'description' => 'Blood Test',
                    'quantity'    => 2,
                    'unit_price'  => 100.00,
                ],
            ],
        ]);

        // Subtotal = 300 + 200 = 500; Total = 500 - 50 = 450
        $response->assertStatus(201)
            ->assertJsonPath('data.subtotal', 500)
            ->assertJsonPath('data.discount', 50)
            ->assertJsonPath('data.total_amount', 450)
            ->assertJsonPath('data.payment_status', 'unpaid')
            ->assertJsonPath('data.is_read_only', false);
    }

    public function test_receptionist_can_edit_items_and_apply_discount_before_payment(): void
    {
        Sanctum::actingAs($this->receptionist);

        $invoice = Invoice::create([
            'patient_id'     => $this->patient->id,
            'subtotal'       => 300.00,
            'discount'       => 0.00,
            'total_amount'   => 300.00,
            'payment_status' => Invoice::STATUS_UNPAID,
            'is_locked'      => false,
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'description' => 'Basic Checkup',
            'quantity'    => 1,
            'unit_price'  => 300.00,
            'total_price' => 300.00,
        ]);

        // Apply discount before payment
        $discountResponse = $this->postJson("/api/invoices/{$invoice->id}/discount", [
            'discount'        => 30.00,
            'discount_reason' => 'Senior citizen discount',
        ]);

        $discountResponse->assertStatus(200)
            ->assertJsonPath('data.discount', 30)
            ->assertJsonPath('data.total_amount', 270);
    }

    public function test_doctor_cannot_modify_invoice_or_apply_discount(): void
    {
        Sanctum::actingAs($this->doctor);

        $invoice = Invoice::create([
            'patient_id'     => $this->patient->id,
            'subtotal'       => 300.00,
            'discount'       => 0.00,
            'total_amount'   => 300.00,
            'payment_status' => Invoice::STATUS_UNPAID,
            'is_locked'      => false,
        ]);

        $discountResponse = $this->postJson("/api/invoices/{$invoice->id}/discount", [
            'discount'        => 30.00,
            'discount_reason' => 'Unauthorized discount attempt',
        ]);

        $discountResponse->assertStatus(403);

        $updateResponse = $this->putJson("/api/invoices/{$invoice->id}", [
            'discount' => 50.00,
        ]);

        $updateResponse->assertStatus(403);
    }

    public function test_invoice_becomes_read_only_after_full_payment(): void
    {
        Sanctum::actingAs($this->receptionist);

        $invoice = Invoice::create([
            'patient_id'     => $this->patient->id,
            'subtotal'       => 200.00,
            'discount'       => 0.00,
            'total_amount'   => 200.00,
            'payment_status' => Invoice::STATUS_UNPAID,
            'is_locked'      => false,
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'description' => 'Ultrasound',
            'quantity'    => 1,
            'unit_price'  => 200.00,
            'total_price' => 200.00,
        ]);

        // Pay in full
        $payResponse = $this->postJson("/api/invoices/{$invoice->id}/pay", [
            'amount_paid' => 200.00,
            'method'      => 'cash',
        ]);

        $payResponse->assertStatus(200)
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.is_locked', true)
            ->assertJsonPath('data.is_read_only', true);

        // Attempting to modify invoice after payment MUST fail
        $attemptEditResponse = $this->putJson("/api/invoices/{$invoice->id}", [
            'discount' => 50.00,
        ]);

        $attemptEditResponse->assertStatus(422)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('is_locked', true);

        // Attempting to apply discount after payment MUST fail
        $attemptDiscountResponse = $this->postJson("/api/invoices/{$invoice->id}/discount", [
            'discount' => 20.00,
        ]);

        $attemptDiscountResponse->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_receptionist_can_process_refund_on_paid_invoice(): void
    {
        Sanctum::actingAs($this->receptionist);

        $invoice = Invoice::create([
            'patient_id'     => $this->patient->id,
            'subtotal'       => 150.00,
            'discount'       => 0.00,
            'total_amount'   => 150.00,
            'payment_status' => Invoice::STATUS_PAID,
            'is_locked'      => true,
        ]);

        $refundResponse = $this->postJson("/api/invoices/{$invoice->id}/refund", [
            'reason' => 'Doctor had an emergency, appointment cancelled after payment',
        ]);

        $refundResponse->assertStatus(200)
            ->assertJsonPath('data.payment_status', 'refunded')
            ->assertJsonPath('data.refund_reason', 'Doctor had an emergency, appointment cancelled after payment')
            ->assertJsonPath('data.refunded_by.role', 'receptionist');

        $this->assertDatabaseHas('invoices', [
            'id'             => $invoice->id,
            'payment_status' => 'refunded',
        ]);
    }

    public function test_doctor_cannot_process_refund(): void
    {
        Sanctum::actingAs($this->doctor);

        $invoice = Invoice::create([
            'patient_id'     => $this->patient->id,
            'subtotal'       => 150.00,
            'total_amount'   => 150.00,
            'payment_status' => Invoice::STATUS_PAID,
            'is_locked'      => true,
        ]);

        $refundResponse = $this->postJson("/api/invoices/{$invoice->id}/refund", [
            'reason' => 'Doctor attempting refund',
        ]);

        $refundResponse->assertStatus(403);
    }

    public function test_cannot_refund_unpaid_invoice(): void
    {
        Sanctum::actingAs($this->receptionist);

        $invoice = Invoice::create([
            'patient_id'     => $this->patient->id,
            'subtotal'       => 100.00,
            'total_amount'   => 100.00,
            'payment_status' => Invoice::STATUS_UNPAID,
            'is_locked'      => false,
        ]);

        $refundResponse = $this->postJson("/api/invoices/{$invoice->id}/refund", [
            'reason' => 'Attempting to refund unpaid invoice',
        ]);

        $refundResponse->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }
}
