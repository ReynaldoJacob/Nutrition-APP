<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación (solo para invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => \Inertia\Inertia::render('Dashboard'))->name('dashboard');
    Route::get('/pacientes', fn() => \Inertia\Inertia::render('Patients'))->name('pacientes');
    Route::get('/pacientes/{id}', fn($id) => \Inertia\Inertia::render('PatientRecord', ['patientId' => $id]))->name('pacientes.show');
});
