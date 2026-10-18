<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Clinic Management System
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('clinic');
})->name('clinic.home');

Route::get('/login', function () {
    return view('clinic', ['view' => 'login']);
})->name('login');

Route::get('/dashboard', function () {
    return view('clinic', ['view' => 'dashboard']);
})->name('dashboard');
