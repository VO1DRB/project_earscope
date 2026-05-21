<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/create-doctor', [AdminController::class, 'createDoctor'])->name('admin.create-doctor');
        Route::post('/admin/store-doctor', [AdminController::class, 'storeDoctor'])->name('admin.store-doctor');
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
