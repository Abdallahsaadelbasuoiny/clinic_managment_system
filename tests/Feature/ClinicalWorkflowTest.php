<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\DiagnosticOrder;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClinicalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_record_visit_issue_prescription_and_order_diagnostics(): void
    {
        $clinic = Clinic::create(['name' => 'Clinic 1']);
        $doctor = $this->createDoctor($clinic, 'doctor@example.test');
        $patient = Patient::create(['clinic_id' => $clinic->id, 'name' => 'name 1']);
        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now(),
            'status' => Appointment::STATUS_CONFIRMED,
        ]);
        $otherDoctor = $this->createDoctor($clinic, 'second-doctor@example.test');
        $otherAppointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'appointment_date' => now()->addHour(),
        ]);
        Sanctum::actingAs($doctor);

        $this->getJson('/api/appointments')
            ->assertOk()
            ->assertJsonCount(1, 'data');
        $this->getJson("/api/appointments/{$otherAppointment->id}")->assertNotFound();

        $recordResponse = $this->postJson('/api/medical-records', [
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'chief_complaint' => 'Headache',
            'examination' => 'No acute findings',
            'diagnosis' => 'Example diagnosis',
            'treatment_plan' => 'Rest and hydration',
        ]);

        $recordResponse->assertCreated()
            ->assertJsonPath('data.patient.name', 'name 1')
            ->assertJsonPath('data.doctor_id', $doctor->id);
        $recordId = $recordResponse->json('data.id');

        $this->postJson("/api/medical-records/{$recordId}/prescriptions", [
            'notes' => 'Take with food',
            'items' => [[
                'medicine_name' => 'Example medicine',
                'dosage' => '500 mg',
                'frequency' => 'Twice daily',
                'duration' => '5 days',
                'quantity' => 10,
            ]],
        ])->assertCreated()
            ->assertJsonPath('data.items.0.medicine_name', 'Example medicine');

        $orderResponse = $this->postJson("/api/medical-records/{$recordId}/diagnostic-orders", [
            'type' => 'lab',
            'test_name' => 'Complete blood count',
        ]);
        $orderResponse->assertCreated()->assertJsonPath('data.status', 'ordered');
        $orderId = $orderResponse->json('data.id');

        $this->patchJson("/api/diagnostic-orders/{$orderId}", [
            'status' => 'completed',
        ])->assertUnprocessable();

        $this->patchJson("/api/diagnostic-orders/{$orderId}", [
            'status' => 'in_progress',
        ])->assertOk()->assertJsonPath('data.status', 'in_progress');

        $this->patchJson("/api/diagnostic-orders/{$orderId}", [
            'status' => 'completed',
            'result' => 'Results recorded',
        ])->assertOk()->assertJsonPath('data.status', 'completed');

        $this->getJson("/api/patients/{$patient->id}/medical-records")
            ->assertOk()
            ->assertJsonPath('data.0.prescriptions.0.items.0.medicine_name', 'Example medicine')
            ->assertJsonPath('data.0.diagnostic_orders.0.result', 'Results recorded');
    }

    public function test_doctors_cannot_create_records_or_orders_for_another_clinic(): void
    {
        $clinic = Clinic::create(['name' => 'Clinic 1']);
        $otherClinic = Clinic::create(['name' => 'Clinic 2']);
        $doctor = $this->createDoctor($clinic, 'doctor@example.test');
        $otherDoctor = $this->createDoctor($otherClinic, 'other-doctor@example.test');
        $patient = Patient::create(['clinic_id' => $otherClinic->id, 'name' => 'name 1']);
        $record = MedicalRecord::create([
            'clinic_id' => $otherClinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'visit_date' => now(),
        ]);
        Sanctum::actingAs($doctor);

        $this->postJson('/api/medical-records', [
            'patient_id' => $patient->id,
            'diagnosis' => 'Example diagnosis',
        ])->assertUnprocessable()->assertJsonValidationErrors('patient_id');

        $this->postJson("/api/medical-records/{$record->id}/diagnostic-orders", [
            'type' => 'imaging',
            'test_name' => 'X-ray',
        ])->assertNotFound();

        $this->getJson("/api/patients/{$patient->id}/medical-records")->assertNotFound();
    }

    public function test_medical_record_must_be_attached_to_the_doctors_own_patient_appointment(): void
    {
        $clinic = Clinic::create(['name' => 'Clinic 1']);
        $doctor = $this->createDoctor($clinic, 'doctor@example.test');
        $otherDoctor = $this->createDoctor($clinic, 'other-doctor@example.test');
        $patient = Patient::create(['clinic_id' => $clinic->id, 'name' => 'name 1']);
        $appointment = Appointment::create([
            'clinic_id' => $clinic->id,
            'patient_id' => $patient->id,
            'doctor_id' => $otherDoctor->id,
            'appointment_date' => now(),
        ]);
        Sanctum::actingAs($doctor);

        $this->postJson('/api/medical-records', [
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Example diagnosis',
        ])->assertUnprocessable()->assertJsonValidationErrors('appointment_id');
    }

    private function createDoctor(Clinic $clinic, string $email): User
    {
        return User::create([
            'clinic_id' => $clinic->id,
            'name' => 'name 1',
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => User::ROLE_DOCTOR,
            'is_active' => true,
        ]);
    }
}
