<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_only_credentials_and_system_resolves_role(): void
    {
        $admin = User::create([
            'name'      => 'name 1',
            'email'     => 'admin@test.com',
            'phone'     => '01011112222',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'admin@test.com',
            'password' => 'secret123',
            // No role field passed in the request!
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'user'   => [
                    'id'    => $admin->id,
                    'email' => 'admin@test.com',
                    'role'  => 'admin', // Resolved from database
                ],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    public function test_login_resolves_receptionist_role_automatically(): void
    {
        User::create([
            'name'      => 'name 2',
            'email'     => 'recep@test.com',
            'phone'     => '01033334444',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'login'    => 'recep@test.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.role', 'receptionist');
    }

    public function test_user_can_login_with_username_param_without_role(): void
    {
        $patient = User::create([
            'name'      => 'name 3',
            'email'     => 'patient@test.com',
            'phone'     => '01055556666',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_PATIENT,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => $patient->name,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.role', 'patient')
            ->assertJsonPath('user.name', $patient->name);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        User::create([
            'name'      => 'name 2',
            'email'     => 'recep2@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'recep2@test.com',
            'password' => 'wrong-pass',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status'  => 'error',
                'message' => 'Invalid email/phone or password.',
            ]);
    }

    public function test_authenticated_user_can_view_profile_and_logout(): void
    {
        $doctor = User::create([
            'name'      => 'name 4',
            'email'     => 'doctor@test.com',
            'password'  => Hash::make('secret123'),
            'role'      => User::ROLE_DOCTOR,
            'is_active' => true,
        ]);

        $token = $doctor->createToken('test-token')->plainTextToken;

        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('user.role', 'doctor');

        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }
}
