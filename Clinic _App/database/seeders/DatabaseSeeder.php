<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with roles, sample clinic, patient, appointment, and invoice.
     */
    public function run(): void
    {
        // 1. Create or get primary clinic
        $clinic = Clinic::firstOrCreate(
            ['name' => 'Clinic 1'],
            [
                'subdomain' => 'clinic1',
                'phone'     => '+201000000000',
                'address'   => 'Location 1',
                'status'    => 'active',
            ]
        );

        // 2. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'clinic_id'            => $clinic->id,
                'name'                 => 'name 1',
                'phone'                => '01000000001',
                'password'             => Hash::make('password123'),
                'role'                 => User::ROLE_ADMIN,
                'is_active'            => true,
                'must_change_password' => false,
            ]
        );

        // 3. Owner User
        User::firstOrCreate(
            ['email' => 'owner@clinic.com'],
            [
                'clinic_id'            => $clinic->id,
                'name'                 => 'name 2',
                'phone'                => '01000000002',
                'password'             => Hash::make('password123'),
                'role'                 => User::ROLE_OWNER,
                'is_active'            => true,
                'must_change_password' => false,
            ]
        );

        // 4. Doctor User
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@clinic.com'],
            [
                'clinic_id'            => $clinic->id,
                'name'                 => 'name 3',
                'phone'                => '01000000003',
                'password'             => Hash::make('password123'),
                'role'                 => User::ROLE_DOCTOR,
                'is_active'            => true,
                'must_change_password' => false,
            ]
        );

        // 5. Receptionist User
        $receptionist = User::firstOrCreate(
            ['email' => 'receptionist@clinic.com'],
            [
                'clinic_id'            => $clinic->id,
                'name'                 => 'name 4',
                'phone'                => '01000000004',
                'password'             => Hash::make('password123'),
                'role'                 => User::ROLE_RECEPTIONIST,
                'is_active'            => true,
                'must_change_password' => false,
            ]
        );

        // 6. Patient Profile (Created by Receptionist)
        $patient = Patient::firstOrCreate(
            ['phone' => '01234567890'],
            [
                'clinic_id'          => $clinic->id,
                'name'               => 'name 5',
                'national_id'        => '29501011234567',
                'date_of_birth'      => '1995-05-12',
                'gender'             => 'male',
                'address'            => 'Location 2',
                'created_by_user_id' => $receptionist->id,
            ]
        );

        // 7. Appointments (One Regular, One Urgent)
        Appointment::firstOrCreate(
            ['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'status' => Appointment::STATUS_PENDING],
            [
                'clinic_id'          => $clinic->id,
                'appointment_date'   => now()->addDays(1)->setTime(10, 0),
                'is_urgent'          => false,
                'notes'              => 'Initial consultation.',
                'created_by_user_id' => $receptionist->id,
            ]
        );

        Appointment::firstOrCreate(
            ['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'status' => Appointment::STATUS_CONFIRMED],
            [
                'clinic_id'          => $clinic->id,
                'appointment_date'   => now()->addHours(2),
                'is_urgent'          => true, // Urgent Booking
                'notes'              => 'Urgent checkup required.',
                'created_by_user_id' => $receptionist->id,
            ]
        );

        // 8. Unpaid Invoice with Discount and Items
        $invoice = Invoice::firstOrCreate(
            ['patient_id' => $patient->id, 'payment_status' => Invoice::STATUS_UNPAID],
            [
                'clinic_id'        => $clinic->id,
                'subtotal'         => 500.00,
                'discount'         => 50.00,
                'discount_reason'  => 'First-visit welcome discount',
                'total_amount'     => 450.00,
                'doctor_share'     => 300.00,
                'clinic_share'     => 150.00,
                'is_locked'        => false,
            ]
        );

        if ($invoice->items()->count() === 0) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => 'Specialist Consultation',
                'quantity'    => 1,
                'unit_price'  => 350.00,
                'total_price' => 350.00,
            ]);

            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => 'Fast Diagnostic Check',
                'quantity'    => 1,
                'unit_price'  => 150.00,
                'total_price' => 150.00,
            ]);
        }
    }
}
