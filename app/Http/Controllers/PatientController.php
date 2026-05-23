<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultationRequest;
use App\Models\Doctor;
use App\Helpers\ActivityLogger;

class PatientController extends Controller
{
    public function dashboard()
    {
        $patient = auth()->user()->patient;

        $consultations = $patient->consultations()->with('doctor')->latest()->get();

        return view('patient.dashboard', compact('consultations'));
    }

    public function createConsultation()
    {
        $doctors = Doctor::all();
        return view('patient.create-consultation', compact('doctors'));
    }

    public function storeConsultation(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required',
            'complaint' => 'required',
        ]);

        $patient = auth()->user()->patient;
        $consultation = ConsultationRequest::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'complaint' => $request->complaint,
            'status' => 'pending',
        ]);
        
        // Log consultation request
        ActivityLogger::logConsultationRequested($consultation, $patient);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Konsultasi berhasil dikirim! Menunggu konfirmasi dari dokter.');
    }
}