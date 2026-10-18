<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
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

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth session
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });

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
    });

    /*
    |--------------------------------------------------------------------------
    | Receptionist-Exclusive Endpoints
    | Responsibilities: Creating Patient Profiles, Invoice Refunds
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:receptionist')->group(function () {
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
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
