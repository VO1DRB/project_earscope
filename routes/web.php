<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', fn () => 'Admin Dashboard');
    });

    Route::middleware(['role:doctor'])->group(function () {
        Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
        Route::get('/doctor/consultations', [DoctorController::class, 'Consultations'])->name('doctor.consultations');
        Route::get('/doctor/patients-profile', [DoctorController::class, 'patientsProfile'])->name('doctor.patients-profile');
        Route::get('/doctor/consultation/{id}/details', [DoctorController::class, 'getConsultationDetails'])->name('consultation.details');
        Route::post('/doctor/consultation/{id}/approve', [DoctorController::class, 'approve'])->name('consultation.approve');
        Route::post('/doctor/consultation/{id}/reject', [DoctorController::class, 'reject'])->name('consultation.reject');
        Route::post('/doctor/consultation/{id}/schedule', [DoctorController::class, 'schedule'])->name('consultation.schedule');
    });

    Route::middleware(['role:patient'])->group(function () {
        Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    });

});

require __DIR__.'/auth.php';
