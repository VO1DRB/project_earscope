<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultationReq;
use App\Models\Doctor;

class PatientController extends Controller
{
    public function dashboard()
    {
        $patient = auth()->user()->patient;

        $consultations = $patient->consultations;

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

        ConsultationReq::create([
            'patient_id' => auth()->user()->patient->id,
            'doctor_id' => $request->doctor_id,
            'complaint' => $request->complaint,
            'status' => 'pending',
        ]);

        return redirect('/patient/dashboard');
    }
}