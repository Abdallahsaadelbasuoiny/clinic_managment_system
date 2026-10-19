<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClinicTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_only_list_and_view_patients_from_their_clinic(): void
    {
        [$clinic, $otherClinic] = $this->createClinics();
        $receptionist = $this->createUser($clinic, User::ROLE_RECEPTIONIST);
        $patient = Patient::create(['clinic_id' => $clinic->id, 'name' => 'name 1']);
        $otherPatient = Patient::create(['clinic_id' => $otherClinic->id, 'name' => 'name 2']);

        Sanctum::actingAs($receptionist);

        $this->getJson('/api/patients')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'name 1');

        $this->getJson("/api/patients/{$otherPatient->id}")->assertNotFound();
        $this->getJson("/api/patients/{$patient->id}")->assertOk();
    }

    public function test_patient_creation_uses_the_authenticated_clinic_not_the_submitted_clinic(): void
    {
        [$clinic, $otherClinic] = $this->createClinics();
        Sanctum::actingAs($this->createUser($clinic, User::ROLE_RECEPTIONIST));

        $response = $this->postJson('/api/patients', [
            'clinic_id' => $otherClinic->id,
            'name' => 'name supplied by client',
            'phone' => '01012345678',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.clinic_id', $clinic->id)
            ->assertJsonPath('data.name', 'name 1');
        $this->assertDatabaseHas('patients', [
            'name' => 'name 1',
            'clinic_id' => $clinic->id,
        ]);
    }

    public function test_existing_person_names_are_anonymized_by_the_data_migration(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'legacy personal name',
            'email' => 'legacy-user@example.test',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_PATIENT,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $patientId = DB::table('patients')->insertGetId([
            'name' => 'legacy patient name',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path('migrations/2026_10_19_000002_anonymize_person_names.php');
        $migration->up();

        $this->assertDatabaseHas('users', ['id' => $userId, 'name' => "name {$userId}"]);
        $this->assertDatabaseHas('patients', ['id' => $patientId, 'name' => "name {$patientId}"]);
    }

    public function test_patient_name_cannot_be_changed_to_a_real_name(): void
    {
        [$clinic] = $this->createClinics();
        $receptionist = $this->createUser($clinic, User::ROLE_RECEPTIONIST);
        $patient = Patient::create(['clinic_id' => $clinic->id, 'name' => 'name 1']);
        Sanctum::actingAs($receptionist);

        $this->putJson("/api/patients/{$patient->id}", [
            'name' => 'private name submitted',
        ])->assertOk()->assertJsonPath('data.name', 'name 1');

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'name' => 'name 1']);
    }

    public function test_appointments_and_invoices_cannot_use_another_clinics_patients(): void
    {
        [$clinic, $otherClinic] = $this->createClinics();
        $receptionist = $this->createUser($clinic, User::ROLE_RECEPTIONIST);
        $doctor = $this->createUser($clinic, User::ROLE_DOCTOR);
        $otherPatient = Patient::create(['clinic_id' => $otherClinic->id, 'name' => 'name 1']);
        Sanctum::actingAs($receptionist);

        $this->postJson('/api/appointments', [
            'patient_id' => $otherPatient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDay()->toDateTimeString(),
        ])->assertUnprocessable()->assertJsonValidationErrors('patient_id');

        $this->postJson('/api/invoices', [
            'patient_id' => $otherPatient->id,
            'items' => [[
                'description' => 'Consultation',
                'quantity' => 1,
                'unit_price' => 100,
            ]],
        ])->assertUnprocessable()->assertJsonValidationErrors('patient_id');
    }

    public function test_appointments_and_invoices_from_another_clinic_are_not_visible(): void
    {
        [$clinic, $otherClinic] = $this->createClinics();
        $receptionist = $this->createUser($clinic, User::ROLE_RECEPTIONIST);
        $otherDoctor = $this->createUser($otherClinic, User::ROLE_DOCTOR);
        $otherPatient = Patient::create(['clinic_id' => $otherClinic->id, 'name' => 'name 1']);
        $otherAppointment = Appointment::create([
            'clinic_id' => $otherClinic->id,
            'patient_id' => $otherPatient->id,
            'doctor_id' => $otherDoctor->id,
            'appointment_date' => now()->addDay(),
        ]);
        $otherInvoice = Invoice::create([
            'clinic_id' => $otherClinic->id,
            'patient_id' => $otherPatient->id,
            'total_amount' => 100,
        ]);

        Sanctum::actingAs($receptionist);

        $this->getJson('/api/appointments')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson("/api/appointments/{$otherAppointment->id}")->assertNotFound();
        $this->getJson('/api/invoices')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson("/api/invoices/{$otherInvoice->id}")->assertNotFound();
    }

    /**
     * @return array{Clinic, Clinic}
     */
    private function createClinics(): array
    {
        return [
            Clinic::create(['name' => 'Clinic A']),
            Clinic::create(['name' => 'Clinic B']),
        ];
    }

    private function createUser(Clinic $clinic, string $role): User
    {
        return User::create([
            'clinic_id' => $clinic->id,
            'name' => "{$role} user",
            'email' => "{$role}-{$clinic->id}@example.test",
            'password' => Hash::make('password123'),
            'role' => $role,
            'is_active' => true,
        ]);
    }
}
