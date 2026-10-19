<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClinicSettingsController;
use App\Http\Controllers\Api\ClinicalRecordController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Clinic Management System
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});
Route::post('/login', [AuthController::class, 'login']);
Route::get('/clinic', [ClinicSettingsController::class, 'show'])->name('clinic.show');

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth session
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });

    Route::middleware('role:admin,owner,receptionist,doctor,nurse')
        ->get('/doctors', [AdminUserController::class, 'listClinicDoctors'])
        ->name('doctors.index');

    /*
    |--------------------------------------------------------------------------
    | Admin / Owner Endpoints
    | Responsibilities: Creating Doctor and Receptionist Profiles
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,owner')->prefix('admin')->group(function () {
        Route::get('/doctors', [AdminUserController::class, 'listDoctors'])->name('admin.doctors.index');
        Route::post('/doctors', [AdminUserController::class, 'createDoctor'])->name('admin.doctors.store');
        Route::get('/receptionists', [AdminUserController::class, 'listReceptionists'])->name('admin.receptionists.index');
        Route::post('/receptionists', [AdminUserController::class, 'createReceptionist'])->name('admin.receptionists.store');
        Route::patch('/staff/{id}', [AdminUserController::class, 'updateStaff'])->name('admin.staff.update');
        Route::get('/clinic', [ClinicSettingsController::class, 'show'])->name('admin.clinic.show');
        Route::put('/clinic', [ClinicSettingsController::class, 'update'])->name('admin.clinic.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Staff Patient Registration
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,owner,receptionist,doctor')
        ->post('/patients', [PatientController::class, 'store'])
        ->name('patients.store');

    /*
    |--------------------------------------------------------------------------
    | Receptionist-Exclusive Endpoints
    | Responsibilities: Invoice Refunds
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:receptionist')->group(function () {
        Route::post('/receptionist/patients', [PatientController::class, 'store']);
        Route::post('/invoices/{id}/refund', [InvoiceController::class, 'refund'])->name('invoices.refund');
    });

    /*
    |--------------------------------------------------------------------------
    | Patients API
    | Viewing, searching, and updating patients
    |--------------------------------------------------------------------------
    */
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::middleware('role:admin,owner,doctor,nurse')->group(function () {
        Route::get('/patients/{patientId}/medical-records', [ClinicalRecordController::class, 'indexForPatient'])
            ->name('patients.medical-records.index');
        Route::get('/patients/{patientId}/diagnostic-orders', [ClinicalRecordController::class, 'indexDiagnosticOrders'])
            ->name('patients.diagnostic-orders.index');
    });

    Route::middleware('role:doctor')->group(function () {
        Route::post('/medical-records', [ClinicalRecordController::class, 'store'])
            ->name('medical-records.store');
        Route::post('/medical-records/{recordId}/prescriptions', [ClinicalRecordController::class, 'storePrescription'])
            ->name('medical-records.prescriptions.store');
        Route::post('/medical-records/{recordId}/diagnostic-orders', [ClinicalRecordController::class, 'storeDiagnosticOrder'])
            ->name('medical-records.diagnostic-orders.store');
        Route::patch('/diagnostic-orders/{orderId}', [ClinicalRecordController::class, 'updateDiagnosticOrder'])
            ->name('diagnostic-orders.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Appointments API
    | Lifecycle: Pending -> Confirmed -> Completed -> Cancelled
    | Supports Urgent flag
    |--------------------------------------------------------------------------
    */
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    /*
    |--------------------------------------------------------------------------
    | Invoices & Billing API
    | Read-only lock upon Payment, Pre-payment Discounts & Item edits
    |--------------------------------------------------------------------------
    */
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::post('/invoices/{id}/discount', [InvoiceController::class, 'applyDiscount'])->name('invoices.apply-discount');
    Route::post('/invoices/{id}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
});
