<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_doctor_profile(): void
    {
        $admin = User::create([
            'name'      => 'name 1',
            'email'     => 'admin@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/doctors', [
            'name'     => 'name supplied by client',
            'email'    => 'doctor2@test.com',
            'phone'    => '01011113333',
            'password' => 'doctorpass123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'doctor')
            ->assertJsonPath('data.name', 'name 2');

        $this->assertDatabaseHas('users', [
            'email' => 'doctor2@test.com',
            'role'  => 'doctor',
        ]);
    }

    public function test_receptionist_cannot_create_doctor_profile(): void
    {
        $receptionist = User::create([
            'name'      => 'name 3',
            'email'     => 'recep@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        Sanctum::actingAs($receptionist);

        $response = $this->postJson('/api/admin/doctors', [
            'name'     => 'name 4',
            'email'    => 'unauth@test.com',
            'password' => 'doctorpass123',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_receptionist_profile(): void
    {
        $admin = User::create([
            'name'      => 'name 1',
            'email'     => 'admin@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/admin/receptionists', [
            'email'    => 'recep5@test.com',
            'phone'    => '01055556666',
            'password' => 'receppass123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'receptionist');

        $this->assertDatabaseHas('users', [
            'email' => 'recep5@test.com',
            'role'  => 'receptionist',
        ]);
    }

    public function test_receptionist_can_create_patient_profile(): void
    {
        $receptionist = User::create([
            'name'      => 'name 3',
            'email'     => 'recep@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        Sanctum::actingAs($receptionist);

        $response = $this->postJson('/api/patients', [
            'name'          => 'name supplied by client',
            'phone'         => '01122223333',
            'national_id'   => '29001011234567',
            'date_of_birth' => '1990-01-01',
            'gender'        => 'male',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'name 1');

        $this->assertDatabaseHas('patients', [
            'name'               => 'name 1',
            'phone'              => '01122223333',
            'created_by_user_id' => $receptionist->id,
        ]);
    }

    public function test_admin_can_create_patient_profile_and_is_saved_as_creator(): void
    {
        $this->assertStaffRoleCanCreatePatient(User::ROLE_ADMIN);
    }

    public function test_doctor_can_create_patient_profile_and_is_saved_as_creator(): void
    {
        $this->assertStaffRoleCanCreatePatient(User::ROLE_DOCTOR);
    }

    private function assertStaffRoleCanCreatePatient(string $role): void
    {
        $staff = User::create([
            'name'      => 'name 7',
            'email'     => $role . '@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => $role,
            'is_active' => true,
        ]);

        Sanctum::actingAs($staff);

        $response = $this->postJson('/api/patients', [
            'phone' => '01199998888',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.created_by_user_id', $staff->id);

        $this->assertDatabaseHas('patients', [
            'phone' => '01199998888',
            'created_by_user_id' => $staff->id,
        ]);
    }
}
