<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnosis;
use App\Models\DiagnosisImage;
use App\Models\ConsultationRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;

class DiagnosisController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            abort(403, 'Doctor not found');
        }

        $consultations = ConsultationRequest::where('doctor_id', $doctor->id)
            ->where('status', 'scheduled')
            ->whereDoesntHave('diagnosis')
            ->with('patient')
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return view('doctor.diagnoses', compact('consultations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consultation_request_id' => 'required|exists:consultation_requests,id',
            'diagnosis_result' => 'required',
            'notes' => 'nullable',
            'image' => 'nullable|image'
        ]);

        $diagnosis = Diagnosis::create([
            'consultation_request_id' => $request->consultation_request_id,
            'diagnosis_result' => $request->diagnosis_result,
            'notes' => $request->notes,
        ]);

        // upload gambar
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('diagnosis_images', 'public');

            DiagnosisImage::create([
                'diagnosis_id' => $diagnosis->id,
                'image_path' => $path,
            ]);
        }
        
        $doctor = Auth::user()->doctor;
        if ($doctor) {
            ActivityLogger::logConsultationUploaded($diagnosis, $doctor);
        }

        return back()->with('success', 'Diagnosis berhasil disimpan');
    }
}