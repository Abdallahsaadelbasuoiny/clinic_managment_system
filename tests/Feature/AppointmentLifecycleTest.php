<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AppointmentLifecycleTest extends TestCase
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
            'email'     => 'recep@test.com',
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
            'phone' => '01012345678',
        ]);
    }

    public function test_appointment_created_as_pending_with_urgent_booking_flag(): void
    {
        Sanctum::actingAs($this->receptionist);

        $response = $this->postJson('/api/appointments', [
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'appointment_date' => now()->addDay()->toDateTimeString(),
            'is_urgent'        => true, // Urgent Booking
            'notes'            => 'Urgent cardiac complaint',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.is_urgent', true);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'status'     => 'pending',
            'is_urgent'  => 1,
        ]);
    }

    public function test_appointment_lifecycle_progression_pending_to_confirmed_to_completed(): void
    {
        Sanctum::actingAs($this->receptionist);

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'appointment_date' => now()->addHours(2),
            'status'           => Appointment::STATUS_PENDING,
            'is_urgent'        => false,
        ]);

        // Step 1: Pending -> Confirmed
        $confirmResponse = $this->patchJson("/api/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_CONFIRMED,
        ]);

        $confirmResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        // Step 2: Confirmed -> Completed
        $completeResponse = $this->patchJson("/api/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_COMPLETED,
        ]);

        $completeResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        // Step 3: Completed -> Pending should be rejected
        $invalidRevertResponse = $this->patchJson("/api/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_PENDING,
        ]);

        $invalidRevertResponse->assertStatus(422)
            ->assertJsonPath('status', 'error');
    }

    public function test_appointment_can_be_cancelled(): void
    {
        Sanctum::actingAs($this->receptionist);

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'appointment_date' => now()->addHours(3),
            'status'           => Appointment::STATUS_PENDING,
        ]);

        $cancelResponse = $this->patchJson("/api/appointments/{$appointment->id}/status", [
            'status' => Appointment::STATUS_CANCELLED,
            'notes'  => 'Patient called to cancel',
        ]);

        $cancelResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');
    }
}
